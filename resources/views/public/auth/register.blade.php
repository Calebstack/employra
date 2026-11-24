@extends('layouts.auth')

@section('title', 'Create your account')
@section('side_title', 'Create your Employra account')
@section('side_text', 'Candidates get CV feedback and one-place application tracking. Companies get a clean workspace to post jobs and review applicants.')

@section('content')
    @php($role = old('role', request('role', 'candidate')))

    <div class="eyebrow mb-2">Free to join</div>
    <h1 class="h2 mb-1">Create your account</h1>
    <p class="text-muted-emp mb-4">Already have an account? <a href="{{ route('login', array_filter(['redirect' => request('redirect')])) }}" class="fw-bold">Sign in</a></p>

    <form method="POST" action="{{ route('register.submit') }}" class="needs-validation" novalidate>
        @csrf
        @if (request('redirect'))
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
        @endif

        {{-- Step 1: who are you? --}}
        <fieldset class="mb-4 pub-role">
            <legend class="form-label">I want to…</legend>
            <div class="row g-2">
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="role-candidate" value="candidate" @checked($role === 'candidate')>
                    <label class="role-card" for="role-candidate">
                        <span class="icon-tile orange mb-2"><i class="bi bi-person-workspace"></i></span>
                        <div class="fw-800 text-navy mt-1">Find a job</div>
                        <div class="small text-muted-emp">Candidate account: CV feedback and tracking</div>
                    </label>
                </div>
                <div class="col-6">
                    <input type="radio" class="btn-check" name="role" id="role-company" value="company" @checked($role === 'company' || $role === 'employer')>
                    <label class="role-card" for="role-company">
                        <span class="icon-tile orange mb-2"><i class="bi bi-buildings"></i></span>
                        <div class="fw-800 text-navy mt-1">Hire talent</div>
                        <div class="small text-muted-emp">Company account: post jobs and review applicants</div>
                    </label>
                </div>
            </div>
        </fieldset>

        <div class="mb-3">
            <label for="name" class="form-label" data-role-text data-candidate="Full name" data-company="Contact name">Full name</label>
            <div class="input-icon">
                <i class="bi bi-person"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Adaeze Okafor" autocomplete="name" required>
                <div class="invalid-feedback">@error('name') {{ $message }} @else Please enter your name. @enderror</div>
            </div>
        </div>

        <div class="mb-3 d-none" data-role-only="company">
            <label for="company_name" class="form-label">Company name</label>
            <div class="input-icon">
                <i class="bi bi-building"></i>
                <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" class="form-control @error('company_name') is-invalid @enderror" placeholder="e.g. BrightPath Technologies" data-required-when-visible>
                <div class="invalid-feedback">@error('company_name') {{ $message }} @else Please enter your company name. @enderror</div>
            </div>
            <div class="form-text">Company profiles may be checked before jobs are published.</div>
        </div>

        <div class="mb-3 d-none" data-role-only="company">
            <label for="industry" class="form-label">Industry</label>
            <select id="industry" name="industry" class="form-select @error('industry') is-invalid @enderror" data-required-when-visible>
                <option value="">Choose an industry</option>
                @foreach (['Technology', 'Finance', 'Healthcare', 'Education', 'Retail', 'Professional Services', 'Other'] as $industry)
                    <option value="{{ $industry }}" @selected(old('industry') === $industry)>{{ $industry }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">@error('industry') {{ $message }} @else Please choose your industry. @enderror</div>
        </div>

        <div class="mb-3">
            <label for="reg-email" class="form-label" data-role-text data-candidate="Email address" data-company="Company email address">Email address</label>
            <div class="input-icon">
                <i class="bi bi-envelope"></i>
                <input id="reg-email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" autocomplete="email" required>
                <div class="invalid-feedback">@error('email') {{ $message }} @else Please enter a valid email address. @enderror</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="reg-password" class="form-label">Password</label>
            <div class="input-icon">
                <i class="bi bi-lock"></i>
                <input id="reg-password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required style="padding-right: 3rem">
                <button type="button" class="password-toggle" data-toggle-password="#reg-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                <div class="invalid-feedback">@error('password') {{ $message }} @else Use at least 8 characters. @enderror</div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2">
                <div class="strength flex-grow-1" data-strength-for="#reg-password"><span></span></div>
                <small class="text-muted-emp fw-semibold" data-strength-label style="min-width:4.5rem"></small>
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <div class="input-icon">
                <i class="bi bi-lock-fill"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Repeat your password" autocomplete="new-password" data-match="#reg-password" required>
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" required>
            <label class="form-check-label small" for="terms">I agree to the <a href="#">Terms of use</a> and <a href="#">Privacy policy</a>.</label>
            <div class="invalid-feedback">You need to accept the terms to continue.</div>
        </div>

        <button type="submit" class="btn btn-emp w-100">
            <span data-role-text data-candidate="Create candidate account" data-company="Create company account">Create account</span>
        </button>
    </form>
@endsection
