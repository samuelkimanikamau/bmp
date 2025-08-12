<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MpesaLog;
use App\Models\Ticket;
use App\Jobs\SendSmsJob;

class MpesaCallbackController extends Controller
{
    public function stkCallback(Request $request)
    {
        // Log the raw incoming request for debugging
        Log::info('M-Pesa Callback Received:', $request->all());

        $data = $request->all();

        // Extract CheckoutRequestID to match with our MpesaLog
        $checkoutId = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;
        $resultCode = $data['Body']['stkCallback']['ResultCode'] ?? null;
        $resultDesc = $data['Body']['stkCallback']['ResultDesc'] ?? null;

        $mpesaLog = MpesaLog::where('checkout_id', $checkoutId)->first();

        if (!$mpesaLog) {
            Log::warning("M-Pesa Callback: No matching MpesaLog for CheckoutRequestID {$checkoutId}");
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback Received']);
        }

        // Update MpesaLog
        $mpesaLog->status = $resultCode;
        $mpesaLog->message = $resultDesc;

        if ($resultCode == 0) {
            // Extract MPESA transaction ID and amount from callback metadata
            $callbackItems = $data['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
            $transactionId = null;
            $amount = null;
            $phoneNumber = null;

            foreach ($callbackItems as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $transactionId = $item['Value'];
                }
                if ($item['Name'] === 'Amount') {
                    $amount = $item['Value'];
                }
                if ($item['Name'] === 'PhoneNumber') {
                    $phoneNumber = $item['Value'];
                }
            }

            $mpesaLog->transaction_id = $transactionId;
            $mpesaLog->amount = $amount;
            $mpesaLog->phone_number = $phoneNumber;
            $mpesaLog->save();

            // Mark Invitee as accepted
            $invitee = $mpesaLog->invitee;
            if ($invitee->status !== 'Accepted') {
                $invitee->status = 'Accepted';
                $invitee->save();
            }

            // Create ticket (Active)
            $ticket = Ticket::create([
                'invitee_id' => $invitee->id,
                'number'     => $transactionId,
                'issued_at'  => now(),
                'status'     => 'Active',
            ]);

            // Send ticket SMS with PDF link
            $pdfUrl = route('ticket.download', ['ticket' => $ticket->id]); // You must create this route
            $smsMessage = "Hi {$invitee->name}, your BPM Skyline Studio ticket #{$ticket->number} is confirmed. Download: {$pdfUrl}";

            SendSmsJob::dispatch($invitee->phone, $smsMessage);
        } else {
            // Failed payment
            $mpesaLog->save();
            Log::warning("M-Pesa payment failed for {$mpesaLog->id}: {$resultDesc}");
        }

        // Always respond with success to Safaricom
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback processed successfully'
        ]);
    }
}