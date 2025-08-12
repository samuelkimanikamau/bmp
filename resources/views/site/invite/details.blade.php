@extends('layouts.site')

@section('title', 'BPM Skyline Studio — Invitation')

{{-- Inject section anchors into the layout navbar --}}
@section('navbar-links')
  <li class="nav-item"><a class="nav-link active d-flex align-items-center gap-1" href="#home"><i class="bi bi-house-door"></i> Home</a></li>
  <li class="nav-item"><a class="nav-link d-flex align-items-center gap-1" href="#location"><i class="bi bi-geo-alt"></i> Location</a></li>
  <li class="nav-item"><a class="nav-link d-flex align-items-center gap-1" href="#experience"><i class="bi bi-star"></i> Experience</a></li>
  <li class="nav-item"><a class="nav-link d-flex align-items-center gap-1" href="#getting-there"><i class="bi bi-car-front"></i> Getting There</a></li>
@endsection

@section('content')
  {{-- HERO / HOME (PARALLAX) --}}
  <section id="home" class="parallax scroll-offset"
    style="--bg: url('/img1.jpeg'); --overlay:.35; --overlay-top:.5">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-8">
          <div class="glass p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
              <div class="text-start">
                <h1 class="display-5 mb-2">{{ $event['title'] }}</h1>
                <p class="mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-calendar-event text-warning"></i>
                  <span class="fw-semibold">Date:</span> {{ $event['date'] }}
                </p>
                <p class="mb-0 d-flex align-items-center gap-2">
                  <i class="bi bi-clock text-warning"></i>
                  <span class="fw-semibold">Time:</span> {{ $event['time'] }}
                </p>
              </div>
              <div class="text-start text-md-end">
                <div class="small text-white-50 d-flex align-items-center justify-content-end gap-1">
                  <i class="bi bi-ticket-perforated"></i> Invite Code
                </div>
                <div class="display-6 fw-bold text-gradient">{{ $invitee->password }}</div>
                <span class="status-badge badge
                  @class([
                    'bg-secondary' => $invitee->status === 'Pending',
                    'bg-success'   => $invitee->status === 'Accepted',
                    'bg-danger'    => $invitee->status === 'Declined',
                  ])">
                  <i class="bi
                    @class([
                      'bi-hourglass-split' => $invitee->status === 'Pending',
                      'bi-check-circle'    => $invitee->status === 'Accepted',
                      'bi-x-circle'        => $invitee->status === 'Declined',
                    ]) me-1"></i>
                  {{ $invitee->status }}
                </span>
              </div>
            </div>

            <p class="lead mb-4">{{ $event['blurb'] }}</p>

            <div class="cta-cluster d-flex flex-wrap justify-content-center gap-3">
              <a class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2"
                 href="{{ route('site.pay.form', ['code' => $invitee->password]) }}">
                <i class="bi bi-check-circle"></i> Register / Confirm
              </a>
              <a class="btn btn-outline-light btn-lg d-flex align-items-center justify-content-center gap-2"
                 href="{{ route('site.decline.form', ['code' => $invitee->password]) }}">
                <i class="bi bi-x-circle"></i> Can't Attend? Decline
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- LOCATION (PARALLAX) --}}
  <section id="location" class="parallax scroll-offset"
    style="--bg: url('/img2.jpeg'); --overlay:.45">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-7">
          <div class="glass p-4 p-md-5">
            <h2 class="h3 mb-4 fw-bold"><i class="bi bi-geo-alt-fill me-2"></i> The Location</h2>
            <p class="mb-3">
              Nestled in the heart of Kenya's Ngong Hills is a rooftop few have seen, and fewer speak of. As the sun sinks behind the ridges, this skyline becomes the backdrop for a different type of gathering that only BPM can create.
            </p>
            <p class="mb-3">Entry is more than just attendance, it's access to an untapped part of the country.</p>
            <p class="small text-white-75 mb-0">
              <i class="bi bi-exclamation-triangle-fill"></i> Exact location details provided to ticket holders only.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- EXPERIENCE (PARALLAX) --}}
  <section id="experience" class="parallax scroll-offset"
    style="--bg: url('/img3.jpeg'); --overlay:.35">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-7">
          <div class="glass p-4 p-md-5">
            <h2 class="h3 mb-4 fw-bold"><i class="bi bi-star-fill me-2"></i> The Experience</h2>
            <p class="mb-3">
              We've heard the city's cry for an event beyond amapiano, and we agree; baby steps. While our sound policy includes private school amapiano and gqom, we're branching into afro/soulful house as well.
            </p>
            <p class="mb-3">
              Your ticket includes redeemable pours at our dry bar; food vendors* will be on site.
            </p>
            <p class="small text-white-75 mb-0">
              <i class="bi bi-emoji-wink-fill"></i> Yes, we know you've been craving a shawarma.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- GETTING THERE (PARALLAX) --}}
  <section id="getting-there" class="parallax scroll-offset"
    style="--bg: url('/img4.jpeg'); --overlay:.45; --overlay-bottom:.5">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-8">
          <div class="glass p-4 p-md-5">
            <h2 class="h3 mb-4 fw-bold"><i class="bi bi-car-front-fill me-2"></i> Getting There</h2>
            <p class="mb-3">
              About 1 hour from the city—the drive is scenic but long. In a rural area where ride-hailing is limited, we've got you covered.
            </p>
            <p class="mb-3">
              A free shuttle to and from the venue is included with your ticket. We run a strict schedule—shuttle leaves the pick-up location* at exactly 3:00 p.m.
            </p>
            <p class="small text-white-75 mb-4">
              <i class="bi bi-info-circle-fill"></i> Pick-up/drop-off location provided to ticket holders only.
            </p>
            <p class="mb-0">
              Driving yourself? Leave Nairobi by 3:00 p.m. Gates close strictly at 4:30 p.m. to avoid recording interruptions.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection