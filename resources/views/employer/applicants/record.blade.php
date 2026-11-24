@extends('layouts.employer')
@section('title', 'Application details')

@section('content')
<div class="page-banner compact">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div><h1 class="page-title">Candidate application</h1><p class="mb-0">{{ $application['job_title'] }}</p></div>
        <div class="d-flex flex-wrap gap-2"><a href="{{ route('employer.jobs') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to jobs</a></div>
    </div>
</div>

<section class="card-emp p-3 p-md-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            @if ($application['photo_url'])
                @include('components.profile-photo-preview', [
                    'src' => $application['photo_url'],
                    'alt' => $application['name'].' profile photo',
                    'id' => 'applicantProfilePhotoPreview',
                    'width' => 64,
                    'height' => 64,
                    'imageClass' => 'avatar object-fit-cover',
                ])
            @else
                <div class="avatar">{{ \App\Support\JobPresenter::initials($application['name']) }}</div>
            @endif
            <div>
                <h2 class="h4 text-navy mb-1">{{ $application['name'] }}</h2>
                <div class="text-muted-emp">{{ $application['email'] }}</div>
                <div class="small text-muted-emp mt-1">{{ $application['headline'] }} · {{ $application['location'] }}</div>
            </div>
        </div>
        @include('employer.partials.app-status', ['status' => $application['status']])
    </div>
    <dl class="row small mt-4 mb-0">
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $application['phone'] }}</dd>
        <dt class="col-sm-3">Availability</dt><dd class="col-sm-9">{{ $application['availability'] }}</dd>
        <dt class="col-sm-3">Expected salary</dt><dd class="col-sm-9">{{ $application['salary'] }}</dd>
        <dt class="col-sm-3">Preferred roles</dt><dd class="col-sm-9">{{ $application['preferred_roles'] ?: 'Not provided' }}</dd>
        <dt class="col-sm-3">Preferred locations</dt><dd class="col-sm-9">{{ $application['preferred_locations'] ?: 'Not provided' }}</dd>
        <dt class="col-sm-3">Preferred work modes</dt><dd class="col-sm-9">{{ $application['preferred_work_modes'] ?: 'Not provided' }}</dd>
        @if ($application['portfolio_url'])
            <dt class="col-sm-3">Portfolio</dt><dd class="col-sm-9"><a href="{{ $application['portfolio_url'] }}" target="_blank" rel="noopener noreferrer">{{ $application['portfolio_url'] }}</a></dd>
        @endif
    </dl>
</section>

<section class="card-emp p-3 p-md-4 mb-4">
    <h2 class="h5 mb-2"><i class="bi bi-briefcase me-1 text-orange"></i>{{ $application['job_title'] }}</h2>
    <p class="small text-muted-emp mb-0">Submitted {{ $application['submitted_at'] }}</p>
    @if ($application['cv'])
        <a href="{{ $application['cv_download_url'] }}" class="btn btn-outline-navy btn-sm mt-3"><i class="bi bi-download me-1"></i>Download submitted CV · {{ $application['cv'] }}</a>
    @else
        <p class="small text-muted-emp mt-3 mb-0">No CV was attached to this application.</p>
    @endif
    @if ($application['cover_note'])
        <hr>
        <h3 class="h6">Cover note</h3>
        <p class="mb-0">{{ $application['cover_note'] }}</p>
    @endif
    @if ($application['summary'])
        <hr>
        <h3 class="h6">Candidate summary</h3>
        <p class="mb-0">{{ $application['summary'] }}</p>
    @endif
</section>

@if (! in_array($application['status'], ['Rejected', 'Withdrawn'], true))
    <section class="card-emp p-3 p-md-4 mb-4">
        <h2 class="h5 mb-3"><i class="bi bi-arrow-repeat me-1 text-orange"></i>Update application</h2>
        @php
            $nextStatuses = match ($application['status']) {
                'New' => ['reviewing' => 'Move to reviewing', 'shortlisted' => 'Shortlist candidate', 'offer' => 'Make an offer'],
                'Reviewing' => ['shortlisted' => 'Shortlist candidate', 'offer' => 'Make an offer'],
                'Shortlisted' => ['reviewing' => 'Move back to reviewing', 'offer' => 'Make an offer'],
                'Interview' => ['reviewing' => 'Move back to reviewing', 'shortlisted' => 'Shortlist candidate', 'offer' => 'Make an offer'],
                default => [],
            };
        @endphp
        @if ($nextStatuses)
            <form method="POST" action="{{ route('employer.applicants.status', $application['id']) }}" class="row g-2 align-items-end mb-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label" for="application-status">New status</label>
                    <select class="form-select" id="application-status" name="status" required>
                        @foreach ($nextStatuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="candidate-message">Message to candidate (optional)</label>
                    <input class="form-control" id="candidate-message" name="message" maxlength="1000">
                </div>
                <div class="col-md-2"><button class="btn btn-emp w-100" type="submit">Update</button></div>
            </form>
        @endif

        @if (in_array($application['status'], ['New', 'Reviewing', 'Shortlisted', 'Interview'], true))
            <form method="POST" action="{{ route('employer.applicants.interviews.store', $application['id']) }}" class="border-top pt-3">
                @csrf
                <h3 class="h6">Schedule an interview</h3>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label" for="scheduled-at">Date and time</label><input class="form-control" type="datetime-local" id="scheduled-at" name="scheduled_at" required></div>
                    <div class="col-md-3"><label class="form-label" for="interview-format">Format</label><select class="form-select" id="interview-format" name="format" required><option value="video">Video call</option><option value="phone">Phone call</option><option value="in_person">In person</option></select></div>
                    <div class="col-md-5"><label class="form-label" for="meeting-details">Meeting link or location</label><input class="form-control" id="meeting-details" name="meeting_details" maxlength="2000"></div>
                    <div class="col-12"><label class="form-label" for="interview-message">Message to candidate (optional)</label><textarea class="form-control" id="interview-message" name="candidate_message" rows="2" maxlength="2000"></textarea></div>
                </div>
                <button class="btn btn-emp mt-3" type="submit">Schedule interview</button>
            </form>
        @endif

        @if (in_array($application['status'], ['New', 'Reviewing', 'Shortlisted', 'Interview'], true))
            <form method="POST" action="{{ route('employer.applicants.decline', $application['id']) }}" class="border-top pt-3 mt-3">
                @csrf
                <label class="form-label" for="decline-message">Decline message (optional)</label>
                <textarea class="form-control mb-2" id="decline-message" name="message" rows="2" maxlength="1000"></textarea>
                <button class="btn btn-outline-danger" type="submit">Decline application</button>
            </form>
        @endif
    </section>
@endif

<section class="card-emp p-3 p-md-4">
    <h2 class="h5 mb-3"><i class="bi bi-calendar-check me-1 text-orange"></i>Interview history</h2>
    @forelse ($application['interviews'] as $interview)
        <div class="border-top py-3">
            <div class="fw-bold">{{ $interview['date'] }} · {{ $interview['time'] }} · {{ $interview['mode'] }}</div>
            <div class="small text-muted-emp">{{ $interview['response'] }}@if ($interview['details']) · {{ $interview['details'] }}@endif</div>
            @if ($interview['response_note'])<p class="small mb-0 mt-1">Candidate note: {{ $interview['response_note'] }}</p>@endif
        </div>
    @empty
        <p class="text-muted-emp mb-0">No interviews have been scheduled.</p>
    @endforelse
</section>
@endsection
