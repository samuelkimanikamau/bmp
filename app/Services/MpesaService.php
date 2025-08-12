<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class MpesaService
{
    /**
     * Format: YmdHis (e.g., 20250816 172501)
     */
    protected function nowTimestamp(): string
    {
        return Carbon::now()->format('YmdHis');
    }

    /**
     * Normalize a phone to 2547XXXXXXXX format.
     */
    protected function normalizeMsisdn(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? $phone;

        if (str_starts_with($phone, '254')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        // If already 7XXXXXXXX, prefix 254
        if (strlen($phone) === 9 && str_starts_with($phone, '7')) {
            return '254' . $phone;
        }

        // Fallback: ensure 254 prefix at least
        return '254' . ltrim($phone, '+');
    }

    /**
     * Base URL depending on environment.
     */
    protected function baseUrl(): string
    {
        $env = strtolower((string) env('MPESA_ENV', 'production'));
        /*return $env === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';*/
        return 'https://api.safaricom.co.ke';
    }

    /**
     * Generate the password for Lipa na M-Pesa.
     * Password = Base64.encode(Shortcode + Passkey + Timestamp)
     */
    public function lipaNaMpesaPassword(?string $timestamp = null): string
    {
        $timestamp = $timestamp ?: $this->nowTimestamp();

        $shortCode = env('MPESA_BUSINESS_SHORTCODE');
        $passKey   = env('SAFARICOM_PASSKEY');

        return base64_encode($shortCode . $passKey . $timestamp);
    }

    /**
     * Generate an M-Pesa access token.
     */
    public function generateAccessToken(): string
    {
        $consumerKey    = env('MPESA_CONSUMER_KEY');
        $consumerSecret = env('MPESA_CONSUMER_SECRET');
        $credentials    = base64_encode($consumerKey . ':' . $consumerSecret);

        $url = $this->baseUrl() . '/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withHeaders([
            'Authorization' => "Basic {$credentials}",
        ])->get($url)->throw();

        $data = $response->json();

        return $data['access_token'] ?? '';
    }

    /**
     * Initiate an STK push request.
     *
     * @param string $phone        07XXXXXXXX or 2547XXXXXXXX
     * @param float|int $amount    Amount in KES
     * @param string $reference    Business reference (max 12 chars recommended by Daraja)
     * @param string $description  Transaction description (shown to user)
     *
     * @return array Parsed Daraja response
     */
    public function stkPush(string $phone, $amount, string $reference, string $description = 'BPM Ticket'): array
    {
        $phone = $this->normalizeMsisdn($phone);

        $shortCode      = env('MPESA_BUSINESS_SHORTCODE', '4135315');
        $timestamp      = $this->nowTimestamp();
        $password       = $this->lipaNaMpesaPassword($timestamp);
        $callbackUrl    = env('MPESA_CALLBACK_URL');
        $accountRef     = mb_substr($reference, 0, 12); // safety
        $transactionDesc= mb_substr($description, 0, 20); // keep it short & clean

        $payload = [
            'BusinessShortCode' => $shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerBuyGoodsOnline',
            'Amount'            => (int) $amount,
            'PartyA'            => $phone,
            'PartyB'            => env('MPESA_TILL_NUMBER'),
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callbackUrl,
            'AccountReference'  => $accountRef,
            'TransactionDesc'   => $transactionDesc,
        ];

        $url = $this->baseUrl() . '/mpesa/stkpush/v1/processrequest';

        Log::info('M-Pesa STK Push — request', ['phone' => $phone, 'amount' => $amount, 'ref' => $accountRef]);

        try {
            $token = $this->generateAccessToken();

            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($url, $payload)
                ->throw();

            $data = $response->json();

            Log::info('M-Pesa STK Push — response', ['data' => $data]);

            // Normalize keys you use elsewhere
            return [
                'MerchantRequestID'   => $data['MerchantRequestID']   ?? null,
                'CheckoutRequestID'   => $data['CheckoutRequestID']   ?? null,
                'ResponseCode'        => $data['ResponseCode']        ?? ($data['errorCode'] ?? null),
                'ResponseDescription' => $data['ResponseDescription'] ?? ($data['errorMessage'] ?? null),
                'raw'                 => $data,
            ];
        } catch (Throwable $e) {
            Log::error('M-Pesa STK Push — error', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            // Return a normalized failure shape
            return [
                'MerchantRequestID'   => null,
                'CheckoutRequestID'   => null,
                'ResponseCode'        => 'error',
                'ResponseDescription' => $e->getMessage(),
                'raw'                 => null,
            ];
        }
    }

    /**
     * Optional: Query STK push result by CheckoutRequestID (if you don’t use callbacks).
     * Implement if needed and call from your polling route.
     */
    public function query(string $checkoutId): array
    {
        $shortCode = env('MPESA_BUSINESS_SHORTCODE');
        $timestamp = $this->nowTimestamp();
        $password  = $this->lipaNaMpesaPassword($timestamp);

        $payload = [
            'BusinessShortCode' => $shortCode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutId,
        ];

        $url = $this->baseUrl() . '/mpesa/stkpushquery/v1/query';

        try {
            $token = $this->generateAccessToken();

            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($url, $payload)
                ->throw();

            $data = $response->json();

            Log::info('M-Pesa STK Query — response', ['data' => $data]);

            return $data;
        } catch (Throwable $e) {
            Log::error('M-Pesa STK Query — error', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return ['error' => $e->getMessage()];
        }
    }
}