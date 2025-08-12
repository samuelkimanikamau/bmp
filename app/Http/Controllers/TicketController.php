<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    /**
     * Stream/download a ticket PDF by ticket number.
     * Route: GET /ticket/{number}.pdf
     */
    public function pdf(string $number)
    {
        $ticket = Ticket::with('invitee')->where('number', $number)->firstOrFail();

        $pdf = Pdf::loadView('tickets.pdf', compact('ticket'))
            ->setPaper([0, 0, 321.26, 264.57], 'portrait'); // 85mm x 70mm in points

        // Stream in browser; change to download() if you prefer forced download
        return $pdf->stream("ticket-{$ticket->number}.pdf");
    }
}