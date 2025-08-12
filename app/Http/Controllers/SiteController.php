<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\Invitee;
use App\Models\MpesaLog;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View as ViewView;
use Illuminate\Support\Facades\View;

class SiteController extends Controller
{
    private const STATUS_PENDING  = 'Pending';
    private const STATUS_ACCEPTED = 'Accepted';
    private const STATUS_DECLINED = 'Declined';

    public function detailsSubmit(Request $request): RedirectResponse
{
    $request->validate([
        'code' => 'required|string'
    ]);

    $code = trim($request->code);

    // Try finding invitee
    $invitee = \App\Models\Invitee::whereRaw('UPPER(password) = ?', [mb_strtoupper($code)])->first();

    if (!$invitee) {
        return redirect()
            ->route('home')
            ->withErrors(['code' => 'Invalid invite code. Please try again.'])
            ->withInput();
    }

    // Redirect to details page
    return redirect()->route('site.details', ['code' => $invitee->password]);
}

    /**
     * Landing/details page: https://bpm.co.ke/{code}
     */
    public function details(string $code): ViewView|RedirectResponse
    {
        $invitee = $this->findByCode($code);

        // Canonicalize to stored uppercase code if user typed different case.
        if ($code !== $invitee->password) {
            return redirect()->route('site.details', ['code' => $invitee->password], 301);
        }

        return View::first([
            'site.invite.details',   // create this (parallax)
            'site.invite',           // fallback
        ], [
            'invitee' => $invitee,
            'event' => [
                'title' => 'BPM Skyline Studio: Session 2',
                'date'  => '16th Aug 2025',
                'time'  => '5–7 p.m. (Peak sunset)',
                'blurb' => "16th August 2025, 5–7 p.m. Peak sunset hours. A live recording as two of Nairobi’s most notable emerging DJs take the stage.",
            ],
        ]);
    }

    /**
     * GET: Registration/Pay page: https://bpm.co.ke/pay/{code}
     */
    public function payForm(string $code): ViewView
    {
        $invitee = $this->findByCode($code);

        return View::first([
            'site.invite.pay',
        ], compact('invitee'));
    }

    /**
     * POST: Start STK Push and redirect to "awaiting" (DO NOT change status yet).
     * Route: POST /pay/{code}
     */
    public function paySubmit(Request $request, string $code, MpesaService $mpesa): RedirectResponse
    {
        $invitee = $this->findByCode($code);

        // Validate phone (07XXXXXXXX, 2547XXXXXXXX, or +2547XXXXXXXX)
        $validated = $request->validate([
            'phone' => ['required', 'regex:/^(?:\+?254|0)?7\d{8}$/'],
        ]);

        // Use user-supplied phone (falls back to invitee->phone if blank, but it's required)
        $phone = $validated['phone'] ?: $invitee->phone;

        // Ticket amount
        $amount = (float) config('bpm.ticket_amount', 1500); // KES 1,500 default

        // Fire STK (reference = invite code)
        $response = $mpesa->stkPush(
            phone: $phone,
            amount: $amount,
            reference: $invitee->password,
            description: 'BPM Skyline Studio Ticket'
        );

        // Persist MpesaLog (do NOT change invitee status here)
        $log = MpesaLog::create([
            'invitee_id'   => $invitee->id,
            'merchant_id'  => $response['MerchantRequestID'] ?? null,
            'checkout_id'  => $response['CheckoutRequestID'] ?? null,
            'phone_number' => $phone,
            'status'       => $response['ResponseCode'] ?? $response['errorCode'] ?? 'pending',
            'message'      => $response['ResponseDescription'] ?? $response['errorMessage'] ?? null,
            'amount'       => $amount,
        ]);

        // If STK request failed to queue, go straight to failed page
        if (($log->status !== '0') && (strtolower((string) $log->status) !== 'pending')) {
            return redirect()
                ->route('site.pay.failed', $log)
                ->with('status', 'M-Pesa STK Push Failed: ' . ($log->message ?? 'Unknown'));
        }

        // Otherwise, show awaiting page that polls status
        return redirect()->route('site.pay.await', $log);
    }

    /**
     * GET: Awaiting confirmation page (spinner + polling).
     * Route: GET /pay/await/{log}
     */
    public function payAwait(MpesaLog $log): ViewView
    {
        $invitee = $log->invitee;

        return View::first([
            'site.invite.pay-await',
        ], compact('invitee', 'log'));
    }

    /**
     * GET (JSON): Poll STK status. Returns next redirect when ready.
     * Route: GET /pay/status/{log}
     *
     * If you have Daraja callbacks, this simply reads updated $log.
     * If not, you can query here via MpesaService->query($log->checkout_id) and update $log accordingly.
     */
    public function payStatus(MpesaLog $log): JsonResponse
    {
        // Example end-state logic:
        if ($log->status === '0' && $log->transaction_id) {
            $log->invitee->update([
                'status' => self::STATUS_ACCEPTED
            ]);
            return response()->json([
                'status'   => 'success',
                'redirect' => route('site.pay.success', ['code' => $log->invitee->password]),
            ]);
        }

        // Non-zero and not "pending" -> failed
        if ($log->status !== '0' && strtolower((string) $log->status) !== 'pending') {
            return response()->json([
                'status'   => 'failed',
                'redirect' => route('site.pay.failed', $log),
            ]);
        }

        // Still pending
        return response()->json(['status' => 'pending']);
    }

    /**
     * GET: Payment success page (post-STK).
     * Route: GET /pay/{code}/success
     */
    public function paySuccess(string $code): ViewView
    {
        $invitee = $this->findByCode($code);

        return View::first([
            'site.invite.pay-success',
        ], compact('invitee'));
    }

    /**
     * GET: Payment failed page.
     * Route: GET /pay/failed/{log}
     */
    public function payFailed(MpesaLog $log): ViewView
    {
        $invitee = $log->invitee;

        return View::first([
            'site.invite.pay-failed',
        ], compact('invitee', 'log'));
    }

    /**
     * Decline: GET form
     * Route: GET /decline/{code}
     */
    public function declineForm(string $code): ViewView
    {
        $invitee = $this->findByCode($code);

        return View::first([
            'site.invite.decline',
        ], compact('invitee'));
    }

    /**
     * Decline: POST submit
     * Route: POST /decline/{code}
     */
    public function declineSubmit(Request $request, string $code): RedirectResponse
    {
        $invitee = $this->findByCode($code);

        if ($invitee->status !== self::STATUS_DECLINED) {
            $invitee->status = self::STATUS_DECLINED;
            // $invitee->declined_at = now(); // if you add the column
            $invitee->save();

            // Optional SMS
            // SendSmsJob::dispatch($invitee->phone, "Hi {$invitee->name}, we’ve recorded your decline. Hope to see you next time!");
        }

        return redirect()
            ->route('site.decline.done', ['code' => $invitee->password])
            ->with('status', 'You have been marked as not attending.');
    }

    /**
     * Decline: done page
     * Route: GET /decline/{code}/done
     */
    public function declineDone(string $code): ViewView
    {
        $invitee = $this->findByCode($code);

        return View::first([
            'site.invite.decline-done',
        ], compact('invitee'));
    }

    /**
     * Helper: find by invite code stored in `password` (case-insensitive).
     */
    protected function findByCode(string $code): Invitee
    {
        $code = trim($code);
        abort_if($code === '', 404);

        return Invitee::whereRaw('UPPER(password) = ?', [mb_strtoupper($code)])->firstOrFail();
    }
}
