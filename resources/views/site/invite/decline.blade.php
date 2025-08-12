@extends('layouts.site')

{{-- Add the anchors into the layout navbar --}}
@section('navbar-links')
  <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
  <li class="nav-item"><a class="nav-link" href="#location">Location</a></li>
  <li class="nav-item"><a class="nav-link" href="#experience">Experience</a></li>
  <li class="nav-item"><a class="nav-link" href="#getting-there">Getting There</a></li>
@endsection

@section('content')
  {{-- HERO / HOME (PARALLAX) --}}
  <section id="home" class="parallax scroll-offset"
    style="--bg: url('/images/skyline-hero.jpg'); --overlay:.35">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-8">
          <div class="glass p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
              <div class="text-start text-md-start">
                <h1 class="h2 mb-2">{{ $event['title'] }}</h1>
                <p class="mb-1"><span class="text-warning fw-semibold">Date:</span> {{ $event['date'] }}</p>
                <p class="mb-0"><span class="text-warning fw-semibold">Time:</span> {{ $event['time'] }}</p>
              </div>
              <div class="text-start text-md-end">
                <div class="small text-white-50">Invite Code</div>
                <div class="display-6 fw-bold">{{ $invitee->password }}</div>
                <span class="status-badge badge
                  @class([
                    'bg-secondary' => $invitee->status === 'Pending',
                    'bg-success'   => $invitee->status === 'Accepted',
                    'bg-danger'    => $invitee->status === 'Declined',
                  ])">
                  {{ $invitee->status }}
                </span>
              </div>
            </div>

            <div class="alert alert-warning text-start">
              Dear <strong>{{ $invitee->name }}</strong>, you’re invited to a live recording as two of Nairobi’s most notable emerging DJs take the stage.
            </div>

            <p class="lead mb-4">{{ $event['blurb'] }}</p>

            <div class="cta-cluster d-flex flex-wrap justify-content-center gap-2">
              <a class="btn btn-primary btn-lg"
                 href="{{ route('site.pay.form', ['code' => $invitee->password]) }}">
                Register / Confirm Attendance
              </a>
              <a class="btn btn-outline-light btn-lg"
                 href="{{ route('site.decline.form', ['code' => $invitee->password]) }}">
                Can’t Attend? Decline
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- HOME DETAILS BLOCK --}}
  <div class="section-block bg-white">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <p class="mb-0">
            16th August 2025. 5 to 7 p.m. Peak sunset hours. A live recording as 2 of Nairobi’s most notable emerging DJs take the stage.
          </p>
        </div>
      </div>
    </div>
  </div>

  {{-- LOCATION (PARALLAX) --}}
  <section id="location" class="parallax scroll-offset"
    style="--bg: url('/images/skyline-location.jpg'); --overlay:.45">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-7">
          <div class="glass p-4 p-md-5">
            <h2 class="h3 mb-3">The Location</h2>
            <p class="mb-2">
              Nestled in the heart of Kenya’s Ngong Hills is a rooftop few have seen, and fewer speak of. As the sun sinks behind the ridges, this skyline becomes the backdrop for a different type of gathering that only BPM can create.
            </p>
            <p class="mb-2">Entry is more than just attendance, it’s access to an untapped part of the country.</p>
            <p class="small text-white-75 mb-0">*Exact location details provided to ticket holders only.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- EXPERIENCE (PARALLAX) --}}
  <section id="experience" class="parallax scroll-offset"
    style="--bg: url('/images/skyline-experience.jpg'); --overlay:.35">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-7">
          <div class="glass p-4 p-md-5">
            <h2 class="h3 mb-3">The Experience</h2>
            <p class="mb-2">
              We’ve heard the city’s cry for an event beyond amapiano, and we agree; baby steps. While our sound policy includes private school amapiano and gqom, we’re branching into afro/soulful house as well.
            </p>
            <p class="mb-2">
              Your ticket includes redeemable pours at our dry bar; food vendors* will be on site.
            </p>
            <p class="small text-white-75 mb-0">*Yes, we know you’ve been craving a shawarma.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- GETTING THERE (PARALLAX) --}}
  <section id="getting-there" class="parallax scroll-offset"
    style="--bg: url('/images/skyline-transport.jpg'); --overlay:.45">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-11 col-lg-8">
          <div class="glass p-4 p-md-5">
            <h2 class="h3 mb-3">Getting There</h2>
            <p class="mb-2">
              About 1 hour from the city—the drive is scenic but long. In a rural area where ride-hailing is limited, we’ve got you covered.
            </p>
            <p class="mb-2">
              A free shuttle to and from the venue is included with your ticket. We run a strict schedule—shuttle leaves the pick-up location* at exactly 3:00 p.m.
            </p>
            <p class="small text-white-75 mb-3">
              *Pick-up/drop-off location provided to ticket holders only.
            </p>
            <p class="mb-0">
              Driving yourself? Leave Nairobi by 3:00 p.m. Gates close strictly at 4:30 p.m. to avoid recording interruptions.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- FINAL CTA --}}
  <div class="section-block bg-white">
    <div class="container">
      <div class="d-flex flex-wrap gap-2 justify-content-center">
        <a class="btn btn-primary btn-lg"
           href="{{ route('site.pay.form', ['code' => $invitee->password]) }}">
          Register / Confirm Attendance
        </a>
        <a class="btn btn-outline-dark btn-lg"
           href="{{ route('site.decline.form', ['code' => $invitee->password]) }}">
          Can’t Attend? Decline
        </a>
      </div>
    </div>
  </div>

  {{-- Sticky mobile CTA --}}
  <div class="d-md-none fixed-bottom bg-dark bg-opacity-75 backdrop-blur border-top p-3">
    <div class="d-flex gap-2">
      <a class="btn btn-primary flex-fill"
         href="{{ route('site.pay.form', ['code' => $invitee->password]) }}">
        Confirm
      </a>
      <a class="btn btn-outline-light flex-fill"
         href="{{ route('site.decline.form', ['code' => $invitee->password]) }}">
        Decline
      </a>
    </div>
  </div>
@endsection