@extends('layouts.site')

@section('title', 'Payment Failed — BPM Skyline Studio')

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
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-11 col-lg-7">
        <div class="glass p-4 p-md-5">
          <h1 class="h4 mb-3 d-flex justify-content-center align-items-center gap-2">
            <i class="bi bi-x-circle-fill text-danger"></i>
            Payment Failed
          </h1>

          <p class="mb-2 text-white-75">
            Sorry {{ $invitee->name }}, the payment could not be processed.
          </p>
          <p class="small text-white-50 mb-4">
            {{ session('status') ?? ($log->message ?: 'An error occurred during the payment process.') }}
          </p>

          <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="{{ route('site.pay.form', ['code' => $invitee->password]) }}" class="btn btn-outline-light">
              Try Again
            </a>
            <a href="{{ route('site.details', ['code' => $invitee->password]) }}" class="btn btn-primary">
              Back to Event
            </a>
          </div>

          <hr class="border-white-25 my-4">

          <div class="text-start small text-white-50">
            <div class="mb-1"><strong>Amount:</strong>
              KES {{ number_format((float) config('bpm.ticket_amount', 1500), 2) }}
            </div>
            <div class="mb-1"><strong>Phone:</strong> {{ $log->phone_number }}</div>
            @if($log->checkout_id)
              <div class="mb-0"><strong>Checkout ID:</strong> {{ $log->checkout_id }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection