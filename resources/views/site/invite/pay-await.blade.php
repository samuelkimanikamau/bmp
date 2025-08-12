@extends('layouts.site')

@section('title', 'Awaiting Payment — BPM Skyline Studio')

@section('navbar-links')
  <li class="nav-item">
    <a class="nav-link" href="{{ route('site.details', ['code' => $invitee->password]) }}">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </li>
@endsection

@section('content')
<section class="parallax scroll-offset"
  style="--bg: url('/img1.jpeg'); --overlay:.50; --overlay-top:.6; --overlay-bottom:.6">
  <div class="container text-center">
    <div class="row justify-content-center">
      <div class="col-11 col-lg-6">
        <div class="glass p-4 p-md-5">
          <h1 class="h4 mb-3">Awaiting Payment Confirmation</h1>
          <p class="mb-4 text-white-75">
            Please authorize the M-Pesa STK prompt sent to <strong>{{ $log->phone_number }}</strong>.
          </p>

          <div class="my-4">
            <div class="spinner-border text-light" style="width: 4rem; height: 4rem;" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>

          <p class="text-white">
            This page will automatically update once payment is confirmed.
          </p>

          <div class="mt-4">
            <a href="{{ route('site.pay.form', ['code' => $invitee->password]) }}" class="btn btn-outline-light btn-sm">
              Cancel &amp; Try Again
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  // Poll payment status every 5s
  (function poll() {
    fetch("{{ route('site.pay.status', $log) }}")
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success' || data.status === 'failed') {
          window.location.href = data.redirect;
        } else {
          setTimeout(poll, 5000);
        }
      })
      .catch(() => setTimeout(poll, 5000));
  })();
</script>
@endsection