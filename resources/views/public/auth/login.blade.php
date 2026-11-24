@extends('layouts.auth')

@section('title', 'Sign in')
@section('side_title', 'Welcome back to Employra')
@section('side_text', 'Sign in to continue your job search, track your applications or manage your vacancies.')

@section('content')
    <div class="eyebrow mb-2">Welcome back</div>
    <h1 class="h2 mb-1">Sign in</h1>
    <p class="text-muted-emp mb-4">New here? <a href="{{ route('register', array_filter(['redirect' => request('redirect')])) }}" class="fw-bold">Create a free account</a></p>

    <form method="POST" action="{{ route('login.submit') }}" class="needs-validation" novalidate>
        @csrf
        @if (request('redirect'))
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" autocomplete="email" required autofocus>
                <div class="invalid-feedback">@error('email') {{ $message }} @else Please enter a valid email address. @enderror</div>
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-1">
                <label for="password" class="form-label">Password</label>
                <a href="{{ route('password.request') }}" class="small fw-bold">Forgot password?</a>
            </div>
            <div class="input-icon">
                <i class="bi bi-lock"></i>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Your password" autocomplete="current-password" required style="padding-right: 3rem">
                <button type="button" class="password-toggle" data-toggle-password="#password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                <div class="invalid-feedback">@error('password') {{ $message }} @else Please enter your password. @enderror</div>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Keep me signed in on this device</label>
        </div>

        <button type="submit" class="btn btn-emp w-100">Sign in</button>
    </form>

    <div class="divider-text my-4">New to Employra?</div>
    <div class="row g-2">
        <div class="col-6"><a href="{{ route('register', ['role' => 'candidate']) }}" class="btn btn-outline-navy w-100">I'm a candidate</a></div>
        <div class="col-6"><a href="{{ route('register', ['role' => 'company']) }}" class="btn btn-outline-navy w-100">I'm a company</a></div>
    </div>
@endsection
