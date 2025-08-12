@extends('layouts.site')

@section('title', 'Confirm Attendance — BPM Skyline Studio')

{{-- Optional: add nav links if you want them visible on this page too --}}
@section('navbar-links')
  <li class="nav-item"><a class="nav-link" href="{{ route('site.details', ['code' => $invitee->password]) }}"><i class="bi bi-arrow-left"></i> Back</a></li>
@endsection

@section('content')

{{-- PARALLAX HERO --}}
<section class="parallax scroll-offset"
  style="--bg: url('/img1.jpeg'); --overlay:.40; --overlay-top:.55; --overlay-bottom:.55">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-11 col-lg-7">
        <div class="glass p-4 p-md-5">
          <h1 class="h3 mb-1">BPM Skyline Studio: Session 2</h1>
          <p class="text-white-50 mb-4">
            <i class="bi bi-calendar-event"></i> 16 Aug 2025 &nbsp;•&nbsp;
            <i class="bi bi-clock"></i> 5–7 p.m. (Peak sunset)
          </p>

          {{-- Status + Code --}}
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 text-start text-md-start">
            <div class="small text-white-50">
              Invitee: <strong class="text-white">{{ $invitee->name }}</strong>
            </div>
            <div class="small">
              <span class="text-white-50">Invite Code</span>:
              <strong class="text-gradient">{{ $invitee->password }}</strong>
            </div>
          </div>

          {{-- Amount --}}
          @php $amount = (float) config('bpm.ticket_amount', 1500); @endphp
          <div class="alert alert-info text-start d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-cash-coin"></i>
            <div>
              <strong>Ticket Price:</strong> KES {{ number_format($amount, 2) }}
            </div>
          </div>

          {{-- Accepted note --}}
          @if ($invitee->status === 'Accepted')
            <div class="alert alert-success text-start d-flex align-items-center gap-2 mb-4">
              <i class="bi bi-check-circle-fill"></i>
              <div>You’re already confirmed. You can proceed with payment below.</div>
            </div>
          @endif

          {{-- FORM --}}
          <form method="POST" action="{{ route('site.pay.submit', ['code' => $invitee->password]) }}" id="confirmForm" class="text-start">
            @csrf

            <div class="mb-3">
              <label for="phone" class="form-label text-white">M‑Pesa Phone Number</label>
              <input type="tel"
                     name="phone"
                     id="phone"
                     class="form-control @error('phone') is-invalid @enderror"
                     value="{{ old('phone', $invitee->phone) }}"
                     placeholder="07XXXXXXXX"
                     required>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text text-white">
                You may change the number if you’d like to pay from a different line. We’ll prompt an STK push to this number.
              </div>
              <p class="text-white mb-2">
          After tapping <strong>Confirm &amp; Pay</strong>, you’ll get an M‑Pesa prompt on your phone. Enter your PIN to complete the purchase.
        </p>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary" id="confirmBtn">
                <span class="btn-label">Confirm &amp; Pay</span>
                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>
          </form>

          {{-- Back link --}}
          <div class="text-center mt-3">
            <a href="{{ route('site.details', ['code' => $invitee->password]) }}" class="small text-white-75 text-white">
              <i class="bi bi-arrow-left"></i> Back to details
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  (function () {
    const form = document.getElementById('confirmForm');
    const btn  = document.getElementById('confirmBtn');
    form?.addEventListener('submit', () => {
      btn?.setAttribute('disabled', 'disabled');
      btn?.querySelector('.spinner-border')?.classList.remove('d-none');
    });
  })();
</script>
@endpush