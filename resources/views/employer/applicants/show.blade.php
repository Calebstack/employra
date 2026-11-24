@extends('layouts.employer')
@section('title', $applicant['name'])

@section('content')
@php $a = $applicant; $labels = ['skills' => 'Required skills', 'experience' => 'Relevant experience', 'education' => 'Education & training', 'keywords' => 'Role keywords']; @endphp
<div class="page-banner">
    <nav aria-label="breadcrumb"><ol class="breadcrumb small mb-3">
        <li class="breadcrumb-item"><a href="{{ route('employer.jobs') }}" class="text-white-50">My jobs</a></li>
        <li class="breadcrumb-item"><a href="{{ route('employer.jobs.show', 1) }}" class="text-white-50">PHP / Laravel Developer</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page">{{ $a['name'] }}</li>
    </ol></nav>
    <div class="d-flex flex-wrap align-items-center gap-3">
        <div class="avatar lg" style="width:64px;height:64px;font-size:1.3rem;border:0;">{{ $a['initials'] }}</div>
        <div class="flex-grow-1">
            <h1 class="page-title mb-1">{{ $a['name'] }}</h1>
            <div>{{ $a['headline'] }} · {{ $a['location'] }} · {{ $a['years'] }} years experience</div>
            <div class="small">{{ $a['email'] }} · Applied {{ $a['applied'] }}</div>
        </div>
        <div>@include('employer.partials.app-status', ['status' => $a['status']])</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i>This match is a guide based only on skills, experience, education and keywords from the CV. It is not a hiring decision, and you can overrule it.</div>

        <section class="card-emp p-3 p-md-4 mb-4">
            <div class="d-flex flex-wrap gap-4 align-items-center mb-3">
                <div class="text-center"><div class="score-ring lg" style="--p: {{ $a['score'] }}"><span>{{ $a['score'] }}</span></div><div class="small fw-bold text-navy mt-2">{{ $a['tier'] }}</div></div>
                <div class="flex-grow-1" style="min-width:240px;"><h2 class="h5"><i class="bi bi-bullseye me-1 text-orange"></i>Why this score</h2><p class="mb-0">{{ $a['summary'] }}</p></div>
            </div>
            @foreach ($a['breakdown'] as $k => $v)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small fw-bold mb-1"><span>{{ $labels[$k] }}</span><span>{{ $v }}%</span></div>
                    <div class="progress-emp"><span style="width: {{ $v }}%"></span></div>
                </div>
            @endforeach
        </section>

        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3"><i class="bi bi-file-earmark-text me-1 text-orange"></i>Evidence from the CV</h2>
            <div class="fw-bold text-navy mb-2"><i class="bi bi-check-circle-fill text-success me-1"></i>Matches ({{ count($a['matched']) }})</div>
            @foreach ($a['matched'] as $m)<div class="evidence mb-2"><strong>{{ $m[0] }}</strong> <span class="small text-muted-emp">— {{ $m[1] }} ({{ $m[2] }})</span></div>@endforeach
            @if ($a['partial'])
                <div class="fw-bold text-navy mt-4 mb-2"><i class="bi bi-dash-circle-fill text-orange me-1"></i>Partly there ({{ count($a['partial']) }})</div>
                @foreach ($a['partial'] as $m)<div class="evidence mb-2"><strong>{{ $m[0] }}</strong> <span class="small text-muted-emp">— {{ $m[1] }} ({{ $m[2] }})</span></div>@endforeach
            @endif
            @if ($a['missing'])
                <div class="fw-bold text-navy mt-4 mb-2"><i class="bi bi-plus-circle-fill text-muted me-1"></i>Not shown on the CV</div>
                @foreach ($a['missing'] as $m)<span class="skill-chip" style="background:#eef1f6;color:#475467;">{{ $m }}</span> @endforeach
                <div class="small text-muted-emp mt-2">Not shown does not mean the candidate lacks it. Consider asking at interview.</div>
            @endif
        </section>

        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3"><i class="bi bi-person-lines-fill me-1 text-orange"></i>Profile</h2>
            <div class="mb-3">@foreach ($a['skills'] as $s)<span class="skill-chip">{{ $s }}</span> @endforeach</div>
            @foreach ($a['experience'] as $e)<div class="entry"><div class="fw-bold text-navy">{{ $e['title'] }}</div><div class="small text-muted-emp">{{ $e['company'] }} · {{ $e['period'] }}</div><p class="small mb-0 mt-1">{{ $e['description'] }}</p></div>@endforeach
            @foreach ($a['education'] as $e)<div class="entry"><div class="fw-bold text-navy">{{ $e['degree'] }}</div><div class="small text-muted-emp">{{ $e['school'] }} · {{ $e['period'] }}</div></div>@endforeach
        </section>

        <section class="card-emp p-4">
            <h2 class="h5 mb-2"><i class="bi bi-chat-left-text me-1 text-orange"></i>Cover note</h2>
            <p class="mb-0">{{ $a['cover_note'] }}</p>
        </section>
    </div>

    <div class="col-lg-4">
        <div class="card-emp p-4 mb-4 position-sticky" style="top: 90px;">
            <h2 class="h6 mb-3"><i class="bi bi-clipboard-check me-1 text-orange"></i>Your decision</h2>
            @if ($a['status'] === 'Rejected')
                <div class="alert alert-danger small">Declined · {{ $a['reject_reason'] ?? 'No reason recorded' }}</div>
                <button class="btn btn-outline-navy w-100">Move back to Reviewing</button>
            @else
                <label class="form-label small" for="stage">Move to stage</label>
                <select id="stage" class="form-select mb-3">@foreach (['New', 'Reviewing', 'Shortlisted', 'Interview', 'Offer'] as $s)<option {{ $a['status'] === $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach</select>
                <button class="btn btn-emp w-100 mb-2"><i class="bi bi-star me-1"></i>Shortlist</button>
                <button class="btn btn-navy w-100 mb-2" type="button" data-bs-toggle="modal" data-bs-target="#scheduleModal"><i class="bi bi-calendar-plus me-1"></i>Schedule interview</button>
                <button class="btn btn-ghost w-100 text-danger" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal">Decline candidate</button>
            @endif
            <hr>
            <a href="#" class="btn btn-outline-navy btn-sm w-100 mb-2"><i class="bi bi-download me-1"></i>Download CV ({{ $a['cv'] }})</a>
            <label class="form-label small mt-2" for="note">Private notes</label>
            <textarea id="note" class="form-control" rows="3" placeholder="Only your team can see this."></textarea>
            <button class="btn btn-outline-navy btn-sm mt-2">Save note</button>
        </div>
    </div>
</div>

<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border-radius:16px;"><form action="{{ route('employer.interviews.store') }}" method="POST" class="modal-body p-4">@csrf
    <h2 class="h5">Schedule interview with {{ $a['name'] }}</h2>
    <div class="row g-3 mt-1">
        <div class="col-6"><label class="form-label">Date</label><input type="date" class="form-control"></div>
        <div class="col-6"><label class="form-label">Time</label><input type="time" class="form-control" value="10:00"></div>
        <div class="col-12"><label class="form-label">Format</label><select class="form-select"><option>Video call</option><option>Phone call</option><option>In person</option></select></div>
        <div class="col-12"><label class="form-label">Link or address</label><input class="form-control" placeholder="Meeting link or office address"></div>
        <div class="col-12"><label class="form-label">Message to candidate</label><textarea class="form-control" rows="3" placeholder="What should they prepare?"></textarea></div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-outline-navy" data-bs-dismiss="modal">Cancel</button><button class="btn btn-emp">Send invitation</button></div>
</form></div></div></div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border-radius:16px;"><form action="{{ route('employer.applicants.decline', $a['id']) }}" method="POST" class="modal-body p-4">@csrf
    <h2 class="h5">Decline {{ $a['name'] }}?</h2>
    <p class="text-muted-emp small">The candidate will be told respectfully. Choose a work-related reason.</p>
    <label class="form-label">Reason</label>
    <select class="form-select mb-3"><option>Does not meet the required skills</option><option>Not enough relevant experience</option><option>Position filled</option><option>Another candidate was a closer fit</option></select>
    <div class="d-flex justify-content-end gap-2"><button type="button" class="btn btn-outline-navy" data-bs-dismiss="modal">Cancel</button><button class="btn btn-navy">Decline candidate</button></div>
</form></div></div></div>
@endsection
