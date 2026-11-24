@extends('layouts.app')
@section('title', 'My profile')

@section('content')
@php
    $c = $candidate;
    $locations = old('preferred_locations', $c['preferred_locations_values']);
    $locations = is_array($locations) && $locations !== [] ? $locations : [''];
@endphp

<div class="card-emp mb-4 overflow-hidden">
    <div class="profile-cover" style="height:110px; background: radial-gradient(420px 200px at 100% 0%, rgba(255,106,0,.45), transparent 60%), linear-gradient(120deg, #0a1f6e, #1245b8);"></div>
    <div class="px-4 pb-4">
        <div class="d-flex flex-wrap align-items-end gap-3" style="margin-top:-42px;">
            @if ($c['photo_url'])
                @include('components.profile-photo-preview', [
                    'src' => $c['photo_url'],
                    'alt' => $c['name'].' profile photo',
                    'id' => 'candidateProfilePhotoPreview',
                    'width' => 68,
                    'height' => 68,
                    'imageClass' => 'avatar lg object-fit-cover',
                ])
            @else
                <div class="avatar lg">{{ $c['initials'] }}</div>
            @endif
            <div class="flex-grow-1 pt-5">
                <h1 class="h4 mb-0">{{ $c['name'] }}</h1>
                <div class="text-muted-emp">{{ $c['headline'] ?: 'Add a professional headline' }} · {{ $c['location'] ?: 'Add your location' }}</div>
                <div class="small text-muted-emp">{{ $c['email'] }}</div>
            </div>
            <button class="btn btn-outline-navy btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#editProfile"><i class="bi bi-pencil me-1"></i>Edit profile</button>
        </div>
        <div class="mt-3" style="max-width: 420px;">
            <div class="d-flex justify-content-between small fw-bold mb-1"><span>Profile {{ $c['completion'] }}% complete</span></div>
            <div class="progress-emp"><span style="width: {{ $c['completion'] }}%"></span></div>
        </div>
    </div>
</div>

<div class="collapse mb-4 @if ($errors->any()) show @endif" id="editProfile">
    <form action="{{ route('candidate.profile.update') }}" method="POST" enctype="multipart/form-data" class="card-emp p-4">
        @csrf
        <h2 class="h5 mb-3">Edit your profile</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="first-name">First name</label>
                <input id="first-name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $c['first_name']) }}" maxlength="120" required>
                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="last-name">Last name</label>
                <input id="last-name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $c['last_name']) }}" maxlength="120" required>
                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="headline">Professional headline</label>
                <input id="headline" name="headline" class="form-control @error('headline') is-invalid @enderror" value="{{ old('headline', $c['headline']) }}" maxlength="160">
                @error('headline')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="location">Location</label>
                <input id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $c['location']) }}" maxlength="120">
                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="phone">Phone</label>
                <input id="phone" name="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $c['phone']) }}" maxlength="30">
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="portfolio-url">Portfolio or LinkedIn URL</label>
                <input id="portfolio-url" name="portfolio_url" type="url" class="form-control @error('portfolio_url') is-invalid @enderror" value="{{ old('portfolio_url', $c['portfolio_url']) }}" maxlength="255" placeholder="https://">
                @error('portfolio_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="summary">About you</label>
                <textarea id="summary" name="summary" class="form-control @error('summary') is-invalid @enderror" rows="4" maxlength="5000">{{ old('summary', $c['summary']) }}</textarea>
                @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="availability">Availability</label>
                <select id="availability" name="availability" class="form-select @error('availability') is-invalid @enderror" required>
                    @foreach (['immediately' => 'Immediately', 'within_2_weeks' => 'Within 2 weeks', 'within_1_month' => 'Within 1 month', 'flexible' => 'Flexible'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('availability', $c['availability_value']) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('availability')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="salary-min">Minimum monthly salary (NGN)</label>
                <input id="salary-min" name="expected_salary_min" type="number" min="0" max="10000000" class="form-control @error('expected_salary_min') is-invalid @enderror" value="{{ old('expected_salary_min', $c['expected_salary_min']) }}">
                @error('expected_salary_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="salary-max">Maximum monthly salary (NGN)</label>
                <input id="salary-max" name="expected_salary_max" type="number" min="0" max="10000000" class="form-control @error('expected_salary_max') is-invalid @enderror" value="{{ old('expected_salary_max', $c['expected_salary_max']) }}">
                @error('expected_salary_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="profile-photo">Profile photo <span class="text-muted-emp fw-normal">(optional)</span></label>
                <input id="profile-photo" name="photo" type="file" accept=".jpg,.jpeg,.png,.webp" class="form-control @error('photo') is-invalid @enderror">
                <div class="form-text">JPG, PNG or WebP, up to 2 MB, minimum 160 × 160 pixels. Only companies you apply to can see it.</div>
                @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @if ($c['photo_url'])
                    <div class="form-check mt-2">
                        <input id="remove-photo" name="remove_photo" type="checkbox" value="1" class="form-check-input">
                        <label class="form-check-label" for="remove-photo">Remove my current photo</label>
                    </div>
                @endif
            </div>
            <div class="col-12">
                <label class="form-label">Preferred locations</label>
                <div id="profile-location-fields">
                    @foreach ($locations as $index => $location)
                        <div class="input-group mb-2">
                            <select name="preferred_locations[]" class="form-select @error('preferred_locations.'.$index) is-invalid @enderror">
                                <option value="">Select a state</option>
                                @foreach (\App\Support\NigerianStates::options() as $stateValue => $stateLabel)
                                    <option value="{{ $stateValue }}" @selected($location === $stateValue)>{{ $stateLabel }}</option>
                                @endforeach
                            </select>
                            @if ($loop->first)
                                <button class="btn btn-outline-navy" type="button" data-add-onboarding-row="profile-location-fields" data-row-template="profile-location-template" data-max-items="5">Add</button>
                            @else
                                <button class="btn btn-outline-navy" type="button" data-remove-onboarding-row>Remove</button>
                            @endif
                        </div>
                        @error('preferred_locations.'.$index)<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    @endforeach
                </div>
                <template id="profile-location-template">
                    <div class="input-group mb-2">
                        <select name="preferred_locations[]" class="form-select">
                            <option value="">Select a state</option>
                            @foreach (\App\Support\NigerianStates::options() as $stateValue => $stateLabel)
                                <option value="{{ $stateValue }}">{{ $stateLabel }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-navy" type="button" data-remove-onboarding-row>Remove</button>
                    </div>
                </template>
                @error('preferred_locations')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-4">
            <button class="btn btn-emp" type="submit">Save changes</button>
            <button type="button" class="btn btn-outline-navy" data-bs-toggle="collapse" data-bs-target="#editProfile">Cancel</button>
        </div>
    </form>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-2"><i class="bi bi-person-lines-fill text-orange me-2"></i>About</h2>
            <p class="mb-0 text-break">{{ $c['summary'] ?: 'Add a short professional summary to your profile.' }}</p>
        </section>
        <section class="card-emp p-4">
            <h2 class="h5 mb-3"><i class="bi bi-sliders text-orange me-2"></i>Job preferences</h2>
            <dl class="small mb-0">
                <dt>Roles</dt><dd>{{ $c['preferred_roles'] ?: 'Not provided' }}</dd>
                <dt>Locations</dt><dd>{{ $c['preferred_locations'] ?: 'Not provided' }}</dd>
                <dt>Work modes</dt><dd>{{ $c['preferred_work_modes'] ?: 'Not provided' }}</dd>
                <dt>Expected salary</dt><dd>{{ $c['expected_salary'] }}</dd>
                <dt>Availability</dt><dd>{{ $c['availability'] }}</dd>
            </dl>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="card-emp p-4">
            <h2 class="h6 mb-3"><i class="bi bi-file-earmark-text text-orange me-2"></i>Your CVs</h2>
            <p class="small text-muted-emp">Your CV is stored privately and is shared with a company only when you select it for an application.</p>
            <a href="{{ route('candidate.cvs') }}" class="btn btn-outline-navy btn-sm w-100">Manage saved CVs</a>
        </section>
    </div>
</div>
@endsection
