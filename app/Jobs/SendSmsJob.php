<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $phone;
    public string $message;

    /**
     * @param string $phone   Raw phone (e.g., 07xx..., 2547xx..., +2547xx...)
     * @param string $message SMS body
     */
    public function __construct(string $phone, string $message)
    {
        $this->onQueue('sms'); // keep SMS off the default queue
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Optional: global rate limit for the "sms" bucket
     */
    public function middleware(): array
    {
        return [
            // requires redis or rate limiter driver; remove if not needed
            new RateLimited('sms'),
        ];
    }

    /**
     * Progressive backoff (seconds). Adjust as needed.
     * e.g., try at 10s, 60s, 5m
     */
    public function backoff(): array
    {
        return [10, 60, 300];
    }

    /**
     * Stop retrying after 10 minutes
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }

    public function handle(): void
    {
        $url       = config('services.sms.url');
        $apiKey    = config('services.sms.apikey');
        $partnerID = config('services.sms.partnerID');
        $shortcode = config('services.sms.shortcode');

        $mobile = $this->normalizePhone($this->phone);

        $payload = [
            'apikey'    => $apiKey,
            'partnerID' => $partnerID,
            'shortcode' => $shortcode,
            'mobile'    => $mobile,
            'message'   => $this->message,
        ];

        try {
            $response = Http::post($url, $payload);

            if ($response->failed()) {
                // Log full context for debugging
                Log::warning('SMS send failed', [
                    'endpoint' => $url,
                    'mobile'   => $mobile,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                // Throw to trigger queue retry/backoff
                $response->throw();
            }

            Log::info('SMS sent', [
                'mobile' => $mobile,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (Exception $e) {
            Log::error('SMS exception', [
                'mobile' => $mobile,
                'error'  => $e->getMessage(),
            ]);
            throw $e; // keep retrying according to backoff/retryUntil
        }
    }

    /**
     * Normalize to E.164 style for KE (+2547XXXXXXXX)
     */
    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $cc = config('services.sms.default_country', '254');

        // 07xxxxxxxx -> +2547xxxxxxxx
        if (str_starts_with($digits, '0')) {
            return '+' . $cc . substr($digits, 1);
        }

        // 2547xxxxxxxx -> +2547xxxxxxxx
        if (str_starts_with($digits, $cc)) {
            return '+' . $digits;
        }

        // Already has +?
        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        // Fallback: assume country code
        return '+' . $cc . $digits;
    }

    /**
     * Optional: log when the job finally fails after all retries.
     */
    public function failed(Exception $e): void
    {
        Log::critical('SendSmsJob permanently failed', [
            'phone'   => $this->phone,
            'message' => $this->message,
            'error'   => $e->getMessage(),
        ]);
    }
}