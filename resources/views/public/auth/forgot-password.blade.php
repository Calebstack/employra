@extends('layouts.auth')

@section('title', 'Reset your password')
@section('side_title', 'Locked out? It happens.')
@section('side_text', 'Enter the email you registered with and we will send you a link to choose a new password.')

@section('content')
    <h1 class="h2 mb-1">Forgot your password?</h1>
    <p class="text-muted-emp mb-4">No problem. Enter your email address and we will send you a reset link.</p>

    <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
        @csrf

        <div class="mb-4">
            <label for="fp-email" class="form-label">Email address</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input id="fp-email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" autocomplete="email" required autofocus>
                <div class="invalid-feedback">@error('email') {{ $message }} @else Please enter a valid email address. @enderror</div>
            </div>
        </div>

        <button type="submit" class="btn btn-emp w-100 mb-3">Send reset link</button>
        <a href="{{ route('login') }}" class="btn btn-ghost w-100"><i class="bi bi-arrow-left me-1"></i> Back to sign in</a>
    </form>
@endsection
