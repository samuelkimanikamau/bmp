@php
    use Illuminate\Support\Str;
    // You can move these to config/bpm.php if you like
    $event = [
        'title' => 'BPM Skyline Studio: Session 2',
        'date'  => 'Saturday, Aug 16 2025',
        'tier'  => 'General Admission',
        'address' => 'Ngong Hills (Exact location sent to ticket holders)',
        'logo'  => public_path('images/bpm-logo.png'), // put a logo at public/images/bpm-logo.png
    ];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - #{{ $ticket->number }}</title>
    <style>
        @page { size: 85mm 70mm; margin: 0; }
        body { margin: 0; padding: 0; }
        .ticket {
            width: 85mm; height: 60mm; margin: 0 auto; padding: 3mm;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            position: relative; font-family: 'Helvetica Neue', Arial, sans-serif;
            display: flex; flex-direction: column; box-sizing: border-box;
        }
        .ticket:not(:last-child){ page-break-after: always; }
        .header { text-align: center; margin-bottom: 2mm; padding-bottom: 2mm; border-bottom: 2px solid #c99510; flex-shrink: 0; }
        .header .logo { width: 70px; height: auto; margin-bottom: 1mm; margin-top: 2mm; }
        .event-title { font-size: 12pt; color: #2c3e50; margin: 0 0 1mm 0; text-transform: uppercase; line-height: 1.1; }
        .event-date { color: #c99510; font-weight: bold; font-size: 9pt; margin: 1mm 0; }
        .venue-info { font-size: 6pt; color: #7f8c8d; line-height: 1.1; }
        .ticket-body { width: 100%; display: block }
        .details { width: 48%; float: left; }
        .qr-section { width: 48%; float: right; text-align: center; }
        .field { margin-bottom: 2mm; }
        .field-label { font-size: 7pt; color: #95a5a6; text-transform: uppercase; letter-spacing: 0.05em; }
        .field-value { font-size: 8pt; color: #2c3e50; font-weight: bold; line-height: 1.3; }
        .qrcode { width: 25mm; height: 25mm; margin-bottom: 1mm; }
        .ticket-number { font-size: 7pt; color: #7f8c8d; word-break: break-word; text-align: center; width: 25mm; padding-left: 30px; }
        .watermark { position: absolute; opacity: 0.08; font-size: 24pt; color: #c99510; transform: translate(-50%, -50%) rotate(-45deg); top: 50%; left: 50%; pointer-events: none; z-index: -1; }
        .footer { flex-shrink: 0; text-align: center; padding-top: 2mm; border-top: 1px dashed #c99510; margin-top: auto; }
        .disclaimer { font-size: 5.5pt; color: #95a5a6; line-height: 1.3; margin: 0; }
        .clearfix::after{ content:""; display:block; clear:both; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="watermark">ADMITS ONE</div>

        <div class="header">
            @if (file_exists($event['logo']))
                <img src="{{ $event['logo'] }}" alt="BPM Logo" class="logo">
            @endif
            <h1 class="event-title">{{ Str::limit($event['title'], 30) }}</h1>
            <div class="event-date">{{ $event['date'] }}</div>
            <div class="venue-info">
                This ticket must be presented for admission.<br />
                Ticket is not refundable unless in the case of event cancellation or postponement.
            </div>
        </div>

        <div class="ticket-body clearfix">
            <div class="details">
                <div class="field">
                    <div class="field-label">Ticket Holder</div>
                    <div class="field-value">{{ $ticket->invitee->name }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Tier</div>
                    <div class="field-value">{{ $event['tier'] }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Event Address</div>
                    <div class="field-value">{{ Str::limit($event['address'], 35) }}</div>
                </div>
                <div class="field">
                    <div class="field-label">Issued</div>
                    <div class="field-value">{{ optional($ticket->issued_at)->format('M d, Y H:i') ?? now()->format('M d, Y H:i') }}</div>
                </div>
            </div>

            <div class="qr-section">
                <img
                    src="data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($ticket->number)) }}"
                    class="qrcode" alt="QR Code">
                <div class="ticket-number">#{{ $ticket->number }}</div>
            </div>
        </div>

        <div class="footer">
            <p class="disclaimer">
                Valid for one entry only. BPM reserves the right of admission.
            </p>
        </div>
    </div>
</body>
</html>