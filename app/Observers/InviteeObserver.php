<?php

namespace App\Observers;

use App\Models\Invitee;
use App\Models\Ticket;
use App\Jobs\SendSmsJob;

class InviteeObserver
{
    public function updated(Invitee $invitee): void
    {
        // Only when status changed to Accepted and no ticket exists yet
        if (! $invitee->wasChanged('status')) return;
        if ($invitee->status !== 'Accepted') return;
        if ($invitee->ticket()->exists()) return;

        // Get the latest successful MpesaLog with a transaction_id
        $log = $invitee->mpesaLogs()
            ->whereNotNull('transaction_id')
            ->orderByDesc('id')
            ->first();

        if (! $log || empty($log->transaction_id)) return;

        $ticket = Ticket::create([
            'invitee_id' => $invitee->id,
            'number'     => $log->transaction_id, // ticket number
            'issued_at'  => now(),
            'status'     => Ticket::STATUS_ACTIVE, // NEW
            'meta'       => [
                'amount'       => $log->amount,
                'checkout_id'  => $log->checkout_id,
                'merchant_id'  => $log->merchant_id,
                'phone_number' => $log->phone_number,
            ],
        ]);

        // Build and send SMS with PDF link
        $link = route('ticket.pdf', ['number' => $ticket->number]);
        $msg  = "Hi {$invitee->name}, your BPM ticket is ready.\n"
              . "Ticket #: {$ticket->number}\n"
              . "Download PDF: {$link}";

        SendSmsJob::dispatch($invitee->phone, $msg)->onQueue('sms');
    }
}