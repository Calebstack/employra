@extends('layouts.auth')

@section('title', 'Set up your candidate profile')
@section('side_title', 'A few details can help you find a better fit.')
@section('side_text', 'Tell us what roles, locations and work styles you prefer. Your CV stays private until you choose to use it.')

@push('styles')
<style>
    .auth-main { align-items: flex-start; justify-content: flex-start; padding-top: 2rem; padding-bottom: 2rem; }
    .auth-box { max-width: 760px; }
    .onboarding-card { border: 1px solid var(--emp-line); border-radius: 18px; padding: 1.5rem; background: #fff; box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 8px 24px rgba(10,31,110,.04); }
    .onboarding-card + .onboarding-card { margin-top: 1rem; }
    @media (max-width: 575.98px) { .onboarding-card { padding: 1rem; } }
</style>
@endpush

@section('content')
@php
    $roles = old('preferred_roles', $profile->preferred_roles ?? ['']);
    $locations = old('preferred_locations', $preferredLocations);
    $selectedModes = old('preferred_work_modes', $profile->preferred_work_modes ?? []);
    $salaryMin = (int) old('expected_salary_min', $profile->expected_salary_min ?? 0);
    $salaryMax = (int) old('expected_salary_max', $profile->expected_salary_max ?? 10000000);
    $roles = is_array($roles) && $roles !== [] ? $roles : [''];
    $locations = is_array($locations) && $locations !== [] ? $locations : [''];
    $selectedModes = is_array($selectedModes) ? $selectedModes : [];
@endphp

<div class="d-flex align-items-center gap-2 small text-muted-emp mb-3">
    <span class="badge-soft">Candidate setup</span>
    <span>Preferences are saved to your profile</span>
</div>
<h1 class="h2 mb-1">What are you looking for?</h1>
<p class="text-muted-emp mb-4">Add your preferences once. You can update your CV later, and you can skip the upload for now.</p>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>We couldn't save your profile yet. Please correct these fields:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('candidate.onboarding.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <section class="onboarding-card">
        <h2 class="h5 mb-1"><i class="bi bi-briefcase text-orange me-2"></i>Roles you want</h2>
        <p class="small text-muted-emp mb-3">Choose suggestions or type any job title. Add up to five.</p>
        <datalist id="role-suggestions">
            @foreach ($roleSuggestions as $roleSuggestion)
                <option value="{{ $roleSuggestion }}">
            @endforeach
        </datalist>
        <div id="preferred-role-fields">
            @foreach ($roles as $index => $role)
                <div class="input-group mb-2">
                    <input
                        id="preferred-role-{{ $index }}"
                        type="text"
                        name="preferred_roles[]"
                        value="{{ $role }}"
                        class="form-control @error('preferred_roles.'.$index) is-invalid @enderror"
                        placeholder="e.g. Product Manager"
                        list="role-suggestions"
                        @if ($loop->first) required @endif
                    >
                    @if (! $loop->first)
                        <button class="btn btn-outline-navy" type="button" data-remove-onboarding-row aria-label="Remove this role">Remove</button>
                    @endif
                </div>
                @error('preferred_roles.'.$index)<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            @endforeach
        </div>
        @error('preferred_roles')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
        <button class="btn btn-outline-navy btn-sm" type="button"
            data-add-onboarding-row="preferred-role-fields"
            data-field-name="preferred_roles[]"
            data-datalist="role-suggestions"
            data-placeholder="Add another role"
            data-max-items="5">
            <i class="bi bi-plus-lg me-1"></i>Add another role
        </button>
    </section>

    <section class="onboarding-card">
        <h2 class="h5 mb-1"><i class="bi bi-geo-alt text-orange me-2"></i>Where do you want to work?</h2>
        <p class="small text-muted-emp mb-3">Select one or more Nigerian states or the Federal Capital Territory. Add up to five.</p>
        <div id="preferred-location-fields">
            @foreach ($locations as $index => $location)
                <div class="input-group mb-2">
                    <select
                        id="preferred-location-{{ $index }}"
                        name="preferred_locations[]"
                        class="form-select @error('preferred_locations.'.$index) is-invalid @enderror"
                        required
                    >
                        <option value="">Select a state</option>
                        @foreach ($stateOptions as $stateValue => $stateLabel)
                            <option value="{{ $stateValue }}" @selected($location === $stateValue)>{{ $stateLabel }}</option>
                        @endforeach
                    </select>
                    @if (! $loop->first)
                        <button class="btn btn-outline-navy" type="button" data-remove-onboarding-row aria-label="Remove this location">Remove</button>
                    @endif
                </div>
                @error('preferred_locations.'.$index)<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            @endforeach
        </div>
        @error('preferred_locations')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
        <button class="btn btn-outline-navy btn-sm" type="button"
            data-add-onboarding-row="preferred-location-fields"
            data-row-template="preferred-location-row-template"
            data-max-items="5">
            <i class="bi bi-plus-lg me-1"></i>Add another state
        </button>
        <template id="preferred-location-row-template">
            <div class="input-group mb-2">
                <select name="preferred_locations[]" class="form-select" required>
                    <option value="">Select a state</option>
                    @foreach ($stateOptions as $stateValue => $stateLabel)
                        <option value="{{ $stateValue }}">{{ $stateLabel }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-navy" type="button" data-remove-onboarding-row aria-label="Remove this state">Remove</button>
            </div>
        </template>
    </section>

    <section class="onboarding-card">
        <h2 class="h5 mb-1"><i class="bi bi-laptop text-orange me-2"></i>Work style</h2>
        <p class="small text-muted-emp mb-3">Select every option you would consider.</p>
        <div class="d-flex flex-wrap gap-3">
            @foreach (['remote' => 'Remote', 'hybrid' => 'Hybrid', 'on_site' => 'On-site'] as $value => $label)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="preferred_work_modes[]" id="mode-{{ $value }}" value="{{ $value }}" @checked(in_array($value, $selectedModes, true))>
                    <label class="form-check-label" for="mode-{{ $value }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
        @error('preferred_work_modes')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        @foreach ($errors->getMessages() as $field => $messages)
            @if (str_starts_with($field, 'preferred_work_modes.'))
                @foreach ($messages as $message)<div class="text-danger small mt-2">{{ $message }}</div>@endforeach
            @endif
        @endforeach
    </section>

    <section class="onboarding-card">
        <h2 class="h5 mb-1"><i class="bi bi-calendar-check text-orange me-2"></i>Availability</h2>
        <label class="form-label mt-3" for="availability">When could you start?</label>
        <select id="availability" name="availability" class="form-select @error('availability') is-invalid @enderror" required>
            <option value="">Choose availability</option>
            @foreach (['immediately' => 'Immediately', 'within_2_weeks' => 'Within 2 weeks', 'within_1_month' => 'Within 1 month', 'flexible' => 'Flexible'] as $value => $label)
                <option value="{{ $value }}" @selected(old('availability', $profile->availability) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('availability')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </section>

    <section class="onboarding-card">
        <h2 class="h5 mb-1"><i class="bi bi-cash-stack text-orange me-2"></i>Desired monthly salary</h2>
        <p class="small text-muted-emp mb-3">Drag the sliders to set a range in Nigerian naira (NGN).</p>
        <div class="mb-3">
            <label class="form-label d-flex justify-content-between" for="expected_salary_min">
                <span>Minimum</span><strong data-salary-min-output></strong>
            </label>
            <input id="expected_salary_min" name="expected_salary_min" type="range" class="form-range" min="0" max="10000000" step="50000" value="{{ min($salaryMin, 10000000) }}">
        </div>
        <div>
            <label class="form-label d-flex justify-content-between" for="expected_salary_max">
                <span>Maximum</span><strong data-salary-max-output></strong>
            </label>
            <input id="expected_salary_max" name="expected_salary_max" type="range" class="form-range" min="0" max="10000000" step="50000" value="{{ min($salaryMax, 10000000) }}">
        </div>
        @error('expected_salary_min')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        @error('expected_salary_max')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        <div class="small text-muted-emp mt-2">Range: ₦0–₦10,000,000 per month.</div>
    </section>

    <section class="onboarding-card">
        <h2 class="h5 mb-1"><i class="bi bi-person-vcard text-orange me-2"></i>Contact and CV</h2>
        <p class="small text-muted-emp mb-3">Your phone number is optional. Your CV is stored privately and can be reused when you apply.</p>
        <div class="mb-3">
            <label class="form-label" for="phone">Phone number <span class="text-muted-emp fw-normal">(optional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $profile->phone) }}" class="form-control @error('phone') is-invalid @enderror" maxlength="30" autocomplete="tel" placeholder="e.g. +234 800 000 0000">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="form-label" for="onboarding-cv">CV <span class="text-muted-emp fw-normal">(optional)</span></label>
            <input id="onboarding-cv" name="cv" type="file" accept=".pdf,.docx" class="form-control @error('cv') is-invalid @enderror">
            <div class="form-text">PDF or DOCX, up to 5 MB. You can add one later from Resume.</div>
            @error('cv')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @if ($currentCv)
                <div class="small text-muted-emp mt-2">
                    Saved CV: <a href="{{ route('candidate.cvs.download', $currentCv->getKey()) }}">{{ $currentCv->original_name }}</a>
                </div>
            @endif
        </div>
    </section>

    <button class="btn btn-emp btn-lg w-100 mt-4" type="submit">Save profile and continue</button>
</form>
@endsection
