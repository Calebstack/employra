@extends('layouts.admin')
@section('title', 'Job details')

@section('content')
<div class="page-head mb-4">
    <a href="{{ route('admin.jobs') }}" class="small fw-bold text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to jobs</a>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mt-3">
        <div>
            <h1 class="page-title mb-1">{{ $job->title }}</h1>
            <p class="text-muted-emp mb-0">{{ $job->company->name }} · {{ $job->category }}</p>
        </div>
        <span class="status {{ $job->status === 'open' ? 'offer' : 'closed' }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3">Job details</h2>
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted-emp">Company</dt><dd class="col-sm-8"><a href="{{ route('admin.companies.show', $job->company) }}">{{ $job->company->name }}</a></dd>
                <dt class="col-sm-4 text-muted-emp">Company account</dt><dd class="col-sm-8">{{ $job->company->contact_name ?: $job->company->name }} ({{ $job->company->email ?: 'No email provided' }})</dd>
                <dt class="col-sm-4 text-muted-emp">Location</dt><dd class="col-sm-8">{{ $job->location }} · {{ $job->work_mode }}</dd>
                <dt class="col-sm-4 text-muted-emp">Type and level</dt><dd class="col-sm-8">{{ $job->job_type }} · {{ $job->experience_level }}</dd>
                <dt class="col-sm-4 text-muted-emp">Category</dt><dd class="col-sm-8">{{ $job->category }}</dd>
                <dt class="col-sm-4 text-muted-emp">Salary (NGN/month)</dt><dd class="col-sm-8">{{ $job->salary_min !== null ? number_format($job->salary_min) : 'Not specified' }} – {{ $job->salary_max !== null ? number_format($job->salary_max) : 'Not specified' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Application deadline</dt><dd class="col-sm-8">{{ $job->deadline?->format('j M Y') ?? 'Not specified' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Minimum experience</dt><dd class="col-sm-8">{{ $job->min_experience_years }} years</dd>
                <dt class="col-sm-4 text-muted-emp">Minimum education</dt><dd class="col-sm-8">{{ $job->minimum_education ?: 'Not specified' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Applicant notifications</dt><dd class="col-sm-8">{{ $job->notify_applicants ? 'Enabled' : 'Disabled' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Applications</dt><dd class="col-sm-8">{{ number_format($job->applications_count) }}</dd>
                <dt class="col-sm-4 text-muted-emp">Posted</dt><dd class="col-sm-8">{{ $job->created_at?->format('j M Y, H:i') ?? 'Not available' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Last updated</dt><dd class="col-sm-8">{{ $job->updated_at?->format('j M Y, H:i') ?? 'Not available' }}</dd>
            </dl>
        </section>
        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3">Summary</h2>
            <p class="mb-0">{{ $job->summary }}</p>
        </section>
        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3">Responsibilities</h2>
            <p class="mb-0 text-break">{{ $job->responsibilities ?: 'Not provided' }}</p>
        </section>
        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3">Required skills</h2>
            @if ($job->required_skills)
                <ul class="mb-0">@foreach ($job->required_skills as $skill)<li>{{ $skill }}</li>@endforeach</ul>
            @else
                <p class="mb-0">Not provided</p>
            @endif
            <h3 class="h6 mt-4 mb-2">Preferred skills</h3>
            @if ($job->preferred_skills)
                <ul class="mb-0">@foreach ($job->preferred_skills as $skill)<li>{{ $skill }}</li>@endforeach</ul>
            @else
                <p class="mb-0">Not provided</p>
            @endif
        </section>
        <section class="card-emp p-4 mb-4">
            <h2 class="h5 mb-3">Benefits</h2>
            <p class="mb-0 text-break">{{ $job->benefits ?: 'Not provided' }}</p>
        </section>
        @if ($job->moderation_note)
            <div class="alert alert-warning"><strong>Moderation note:</strong> {{ $job->moderation_note }}</div>
        @endif
    </div>
    <div class="col-lg-4">
        <section class="card-emp p-4">
            <h2 class="h5 mb-3">Administration</h2>
            @if ($job->status === 'open')
                <a href="{{ route('jobs.show', $job->slug) }}" class="btn btn-outline-navy w-100 mb-2">View public listing</a>
                <form method="POST" action="{{ route('admin.jobs.take-down', $job) }}">
                    @csrf
                    <label class="form-label" for="moderation-note">Reason for taking down</label>
                    <textarea id="moderation-note" name="reason" class="form-control mb-2" maxlength="2000">Removed by an administrator.</textarea>
                    <button class="btn btn-danger w-100" type="submit">Take down job</button>
                </form>
            @elseif ($job->status === 'taken_down')
                <form method="POST" action="{{ route('admin.jobs.keep-live', $job) }}">
                    @csrf
                    <button class="btn btn-emp w-100" type="submit">Keep live</button>
                </form>
            @else
                <p class="small text-muted-emp mb-0">This job is not currently published.</p>
            @endif
        </section>
    </div>
</div>
@endsection
