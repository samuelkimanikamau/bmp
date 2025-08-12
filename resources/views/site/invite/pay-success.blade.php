@extends('layouts.site')

@section('title', 'You’re Confirmed — BPM Skyline Studio')

@section('navbar-links')
  <li class="nav-item">
    <a class="nav-link" href="{{ route('site.details', ['code' => $invitee->password]) }}">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </li>
@endsection

@section('content')
@php
  $amount = (float) config('bpm.ticket_amount', 1500);
@endphp

<section class="parallax scroll-offset"
  style="--bg: url('/img1.jpeg'); --overlay:.45; --overlay-top:.6; --overlay-bottom:.6">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-11 col-lg-7">
        <div class="glass p-4 p-md-5">
          <div class="mb-2 display-6">You’re Confirmed 🎉</div>
          <p class="text-white-75 mb-4">
            Thanks <strong class="text-white">{{ $invitee->name }}</strong>. Your registration has been received.
          </p>

          <div class="row g-3 text-start text-white-75">
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <div class="small text-white-50">Invite Code</div>
                <div class="h4 mb-0 text-gradient">{{ $invitee->password }}</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <div class="small text-white-50">Ticket Price</div>
                <div class="h4 mb-0">KES {{ number_format($amount, 2) }}</div>
              </div>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
            {{-- If you add a receipt or dashboard, link it here --}}
            <a href="{{ route('site.details', ['code' => $invitee->password]) }}" class="btn btn-primary">
              Back to Event
            </a>
          </div>

          <p class="small text-white mt-3 mb-0">
            You’ll receive an SMS with your ticket.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection