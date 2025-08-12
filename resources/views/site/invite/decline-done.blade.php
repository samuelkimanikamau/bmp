@extends('layouts.site')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4 text-center">
        <div class="display-6 mb-3">We’ve noted your response</div>
        <p class="text-muted">Thanks {{ $invitee->name }}. We hope to host you at a future session.</p>
        <a href="{{ route('site.details', ['code' => $invitee->password]) }}" class="btn btn-secondary">
          Back to Event
        </a>
      </div>
    </div>
  </div>
</div>
@endsection