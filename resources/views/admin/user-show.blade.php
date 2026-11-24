@extends('layouts.admin')
@section('title', 'Candidate details')

@section('content')
@php
    $profile = $user->candidateProfile;
    $cvs = $profile?->cvs ?? collect();
@endphp

<div class="page-banner slate">
    <a href="{{ route('admin.users') }}" class="small fw-bold text-decoration-none text-white-50"><i class="bi bi-arrow-left me-1"></i>Back to users</a>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mt-3">
        <div class="d-flex align-items-center gap-3">
            @if ($user->candidateProfile?->photo_path)
                @include('components.profile-photo-preview', [
                    'src' => route('admin.users.photo', $user),
                    'alt' => $user->name.' profile photo',
                    'id' => 'adminCandidatePhotoPreview',
                    'width' => 64,
                    'height' => 64,
                    'imageClass' => 'avatar object-fit-cover',
                ])
            @else
                <div class="avatar" aria-hidden="true">{{ \App\Support\JobPresenter::initials($user->name) }}</div>
            @endif
            <div>
                <h1 class="page-title h3 mb-1">{{ $user->name }}</h1>
                <p>{{ $user->email }} · Candidate account</p>
            </div>
        </div>
        <span class="status {{ $user->status === 'active' ? 'offer' : 'closed' }}">{{ ucfirst($user->status) }}</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <section class="card-emp h-100">
    <div class="card-head"><h2 class="h6 mb-0">Account details</h2></div>
    <div class="p-4">
<dl class="row mb-0">
                <dt class="col-sm-5 text-muted-emp">Name</dt><dd class="col-sm-7">{{ $user->name }}</dd>
                <dt class="col-sm-5 text-muted-emp">Email</dt><dd class="col-sm-7 text-break">{{ $user->email }}</dd>
                <dt class="col-sm-5 text-muted-emp">Email verified</dt><dd class="col-sm-7">{{ $user->email_verified_at?->format('j M Y, H:i') ?? 'Not verified' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Account status</dt><dd class="col-sm-7">{{ ucfirst($user->status) }}</dd>
                <dt class="col-sm-5 text-muted-emp">Registered</dt><dd class="col-sm-7">{{ $user->created_at?->format('j M Y, H:i') ?? 'Not available' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Last sign-in</dt><dd class="col-sm-7">{{ $user->last_login_at?->format('j M Y, H:i') ?? 'No sign-in recorded' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Applications</dt><dd class="col-sm-7">{{ $user->applications->count() }}</dd>
            </dl>
    </div>
</section>
    </div>
    <div class="col-lg-7">
        <section class="card-emp h-100">
    <div class="card-head"><h2 class="h6 mb-0">Candidate profile</h2></div>
    <div class="p-4">
@if ($profile)
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted-emp">Headline</dt><dd class="col-sm-8">{{ $profile->headline ?: 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Phone</dt><dd class="col-sm-8">{{ $profile->phone ?: 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Location</dt><dd class="col-sm-8">{{ $profile->location ?: 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Portfolio</dt><dd class="col-sm-8 text-break">{{ $profile->portfolio_url ?: 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Preferred roles</dt><dd class="col-sm-8">{{ $profile->preferred_roles ? implode(', ', $profile->preferred_roles) : 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Work modes</dt><dd class="col-sm-8">{{ $profile->preferred_work_modes ? implode(', ', $profile->preferred_work_modes) : 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Preferred locations</dt><dd class="col-sm-8">{{ $profile->preferred_locations ? implode(', ', $profile->preferred_locations) : 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Expected salary</dt>
                    <dd class="col-sm-8">
                        @if ($profile->expected_salary_min !== null || $profile->expected_salary_max !== null)
                            NGN {{ $profile->expected_salary_min !== null ? number_format($profile->expected_salary_min) : 'Not specified' }}
                            - NGN {{ $profile->expected_salary_max !== null ? number_format($profile->expected_salary_max) : 'Not specified' }}
                        @else
                            Not provided
                        @endif
                    </dd>
                    <dt class="col-sm-4 text-muted-emp">Availability</dt><dd class="col-sm-8">{{ $profile->availability ?: 'Not provided' }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Profile visibility</dt><dd class="col-sm-8">{{ str_replace('_', ' ', ucfirst($profile->visibility)) }}</dd>
                    <dt class="col-sm-4 text-muted-emp">Profile completion</dt><dd class="col-sm-8">{{ $profile->completion }}%</dd>
                    <dt class="col-sm-4 text-muted-emp">Onboarding completed</dt><dd class="col-sm-8">{{ $profile->onboarding_completed_at?->format('j M Y, H:i') ?? 'Not completed' }}</dd>
                </dl>
                <h3 class="h6 mt-4 mb-2">Summary</h3>
                <p class="mb-0 text-break">{{ $profile->summary ?: 'No summary provided.' }}</p>
            @else
                <p class="text-muted-emp mb-0">This candidate has not created a profile yet.</p>
            @endif
    </div>
</section>
    </div>
</div>

<section class="card-emp mb-4">
    <div class="card-head"><h2 class="h6 mb-0">Candidate CVs</h2><span class="small text-muted-emp">{{ $cvs->count() }} stored</span></div>
    <div class="p-4">
@forelse ($cvs as $cv)
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-top py-3">
            <div>
                <div class="fw-bold">{{ $cv->original_name }} @if ($cv->is_primary)<span class="badge-soft ms-1">Primary</span>@endif</div>
                <div class="small text-muted-emp">{{ strtoupper(pathinfo($cv->original_name, PATHINFO_EXTENSION)) }} · {{ $cv->size_bytes >= 1048576 ? number_format($cv->size_bytes / 1048576, 1).' MB' : number_format(max(1, (int) ceil($cv->size_bytes / 1024))).' KB' }} · {{ ucfirst($cv->status) }} · Uploaded {{ $cv->created_at?->format('j M Y') }}</div>
            </div>
            <a href="{{ route('admin.users.cvs.download', [$user, $cv]) }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-download me-1"></i>Download CV</a>
        </div>
    @empty
        <p class="text-muted-emp mb-0">No CVs have been uploaded.</p>
    @endforelse
    <p class="small text-muted-emp border-top pt-3 mb-0">CV downloads are restricted to administrators and recorded in the activity log.</p>
    </div>
</section>

<section class="card-emp">
    <div class="card-head"><h2 class="h6 mb-0">Application history</h2><span class="small text-muted-emp">{{ $user->applications->count() }} total</span></div>
    <div class="p-4">
@forelse ($user->applications as $application)
        <article class="border-top py-3">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <a href="{{ route('admin.jobs.show', $application->job) }}" class="fw-bold text-decoration-none">{{ $application->job->title }}</a>
                    <div class="small text-muted-emp">{{ $application->job->company->name }} · Submitted {{ $application->created_at?->format('j M Y, H:i') }}</div>
                </div>
                <span class="status submitted">{{ \App\Support\JobApplicationPresenter::statusLabel($application->status) }}</span>
            </div>
            <p class="mb-1 mt-2 text-break">{{ $application->cover_note ?: 'No cover note provided.' }}</p>
            <div class="small text-muted-emp">CV attached: {{ $application->cv?->original_name ?? 'No CV attached' }}</div>
            <div class="small text-muted-emp">Availability at application: {{ \App\Support\CandidateProfilePresenter::availabilityLabel($application->availability) }}</div>
            <div class="small text-muted-emp">
                Expected salary at application:
                @if ($application->expected_salary_min !== null || $application->expected_salary_max !== null)
                    NGN {{ $application->expected_salary_min !== null ? number_format($application->expected_salary_min) : 'Not specified' }}
                    - NGN {{ $application->expected_salary_max !== null ? number_format($application->expected_salary_max) : 'Not specified' }}
                @else
                    Not provided
                @endif
            </div>
            <div class="mt-3">
                <div class="small fw-bold">Status history</div>
                @forelse ($application->statusHistory as $history)
                    <div class="small text-muted-emp">{{ $history->created_at?->format('j M Y, H:i') }} · {{ \App\Support\JobApplicationPresenter::statusLabel($history->status) }}@if ($history->message) · {{ $history->message }}@endif</div>
                @empty
                    <div class="small text-muted-emp">No status history recorded.</div>
                @endforelse
            </div>
            @if ($application->interviews->isNotEmpty())
                <div class="mt-3">
                    <div class="small fw-bold">Interviews</div>
                    @foreach ($application->interviews as $interview)
                        <div class="small text-muted-emp">
                            {{ $interview->scheduled_at?->format('j M Y, H:i') }} · {{ ucfirst(str_replace('_', ' ', $interview->format)) }}
                            · {{ ucfirst(str_replace('_', ' ', $interview->candidate_response)) }}
                            @if ($interview->response_note) · Candidate note: {{ $interview->response_note }}@endif
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    @empty
        <p class="text-muted-emp mb-0">This candidate has not submitted any applications.</p>
    @endforelse
    </div>
</section>
@endsection
