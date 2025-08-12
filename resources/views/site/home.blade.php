@extends('layouts.site')

@section('content')
<style>
    .parallax {
        background-image: url('{{ asset('img1.jpeg') }}');
        height: 100vh;
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .parallax::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.55);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        color: #fff;
        text-align: center;
        padding: 20px;
        max-width: 500px;
        width: 100%;
    }

    .hero-content h1 {
        font-size: 2rem;
        font-weight: bold;
    }

    .hero-content p {
        font-size: 1.1rem;
        margin-bottom: 25px;
    }

    .form-control {
        border-radius: 30px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .btn-custom {
        border-radius: 30px;
        padding: 0.6rem 1.5rem;
        font-weight: bold;
    }
</style>

<div class="parallax">
    <div class="hero-content">
        <h1>BPM Skyline Studio</h1>
        <p>Enter your invite code to confirm attendance</p>

        <form action="{{ route('site.details.submit') }}" method="POST" class="mt-4">
    @csrf
    <div class="input-group mb-3">
        <input type="password" name="code" class="form-control @error('code') is-invalid @enderror"
               placeholder="Enter your invite code" value="{{ old('code') }}" required>
        <button class="btn btn-warning btn-custom" type="submit">Proceed</button>
        @error('code')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</form>
    </div>
</div>
@endsection