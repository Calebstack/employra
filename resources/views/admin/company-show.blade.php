@extends('layouts.admin')
@section('title', 'Company details')

@section('content')
<div class="page-banner slate">
    <a href="{{ route('admin.companies') }}" class="small fw-bold text-decoration-none text-white-50"><i class="bi bi-arrow-left me-1"></i>Back to companies</a>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mt-3">
        <div class="d-flex align-items-center gap-3">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $company->name }} logo" width="72" height="72" class="rounded object-fit-cover">
            @else
                <div class="co-logo" style="background:#1558d6;width:72px;height:72px;font-size:1.4rem;" aria-hidden="true">{{ \App\Support\JobPresenter::initials($company->name) }}</div>
            @endif
            <div>
                <h1 class="page-title h3 mb-1">{{ $company->name }}</h1>
                <p>{{ $company->industry }} · {{ $company->slug }}</p>
            </div>
        </div>
        <span class="status {{ $company->status === 'verified' ? 'offer' : ($company->status === 'pending' ? 'interview' : 'closed') }}">{{ ucfirst($company->status) }}</span>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['label' => 'Total jobs', 'value' => $jobCount, 'icon' => 'briefcase'],
        ['label' => 'Open jobs', 'value' => $openJobCount, 'icon' => 'briefcase-fill'],
        ['label' => 'Applications', 'value' => $applicationsCount, 'icon' => 'people'],
        ['label' => 'Open reports', 'value' => $reportCounts['open'], 'icon' => 'flag'],
    ] as $stat)
        <div class="col-6 col-xl-3">
            <div class="card-emp stat-card h-100">
                <div class="icon-tile {{ $loop->index === 3 ? 'orange' : ($loop->index === 2 ? 'green' : '') }}"><i class="bi bi-{{ $stat['icon'] }}"></i></div>
                <div><div class="num">{{ number_format($stat['value']) }}</div><div class="lbl">{{ $stat['label'] }}</div></div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <section class="card-emp h-100">
    <div class="card-head"><h2 class="h6 mb-0">Company profile</h2></div>
    <div class="p-4">
<dl class="row mb-0">
                <dt class="col-sm-4 text-muted-emp">Company name</dt><dd class="col-sm-8">{{ $company->name }}</dd>
                <dt class="col-sm-4 text-muted-emp">Industry</dt><dd class="col-sm-8">{{ $company->industry }}</dd>
                <dt class="col-sm-4 text-muted-emp">Company slug</dt><dd class="col-sm-8">{{ $company->slug }}</dd>
                <dt class="col-sm-4 text-muted-emp">Headquarters / location</dt><dd class="col-sm-8">{{ $company->location ?: 'Not provided' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Company size</dt><dd class="col-sm-8">{{ $company->company_size ?: 'Not provided' }}</dd>
                <dt class="col-sm-4 text-muted-emp">Website</dt>
                <dd class="col-sm-8">
                    @if ($company->website)
                        <a href="{{ \Illuminate\Support\Str::startsWith($company->website, ['http://', 'https://']) ? $company->website : 'https://'.$company->website }}" target="_blank" rel="noopener noreferrer">{{ $company->website }}</a>
                    @else
                        Not provided
                    @endif
                </dd>
                <dt class="col-sm-4 text-muted-emp">Registration number</dt><dd class="col-sm-8">{{ $company->registration_number ?: 'Not provided' }}</dd>
            </dl>
            <h3 class="h6 mt-4 mb-2">About the company</h3>
            <p class="mb-0 text-break">{{ $company->description ?: 'No company description provided.' }}</p>
    </div>
</section>
    </div>
    <div class="col-lg-5">
        <section class="card-emp h-100">
    <div class="card-head"><h2 class="h6 mb-0">Company account</h2></div>
    <div class="p-4">
<dl class="row mb-0">
                <dt class="col-sm-5 text-muted-emp">Contact</dt><dd class="col-sm-7">{{ $company->contact_name ?: 'Not provided' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Sign-in email</dt><dd class="col-sm-7 text-break">{{ $company->email ?: 'Not provided' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Account status</dt><dd class="col-sm-7">{{ ucfirst($company->account_status ?? 'inactive') }}</dd>
                <dt class="col-sm-5 text-muted-emp">Verification</dt><dd class="col-sm-7">{{ ucfirst($company->status) }}</dd>
                <dt class="col-sm-5 text-muted-emp">Email verified</dt><dd class="col-sm-7">{{ $company->email_verified_at?->format('j M Y, H:i') ?? 'Not verified' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Last sign-in</dt><dd class="col-sm-7">{{ $company->last_login_at?->format('j M Y, H:i') ?? 'No sign-in recorded' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Registered</dt><dd class="col-sm-7">{{ $company->created_at?->format('j M Y, H:i') ?? 'Not available' }}</dd>
                <dt class="col-sm-5 text-muted-emp">Last updated</dt><dd class="col-sm-7">{{ $company->updated_at?->format('j M Y, H:i') ?? 'Not available' }}</dd>
            </dl>
            @if ($company->status === 'verified')
                <a href="{{ route('companies.show', $company->slug) }}" class="btn btn-outline-navy btn-sm mt-3" target="_blank" rel="noopener noreferrer">View public profile</a>
            @endif
    </div>
</section>
    </div>
</div>

@if ($company->status === 'pending')
    <div class="d-flex flex-wrap gap-2 mb-4">
        <form method="POST" action="{{ route('admin.companies.verify', $company) }}">
            @csrf
            <button type="submit" class="btn btn-emp"><i class="bi bi-check-lg me-1"></i>Verify company</button>
        </form>
        <form method="POST" action="{{ route('admin.companies.reject', $company) }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Reject company</button>
        </form>
    </div>
@endif

<section class="card-emp mb-4">
    <div class="card-head"><h2 class="h6 mb-0">Job listings</h2><span class="small text-muted-emp">{{ number_format($jobCount) }} total</span></div>
    <div class="p-4">
@forelse ($jobs as $job)
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 border-top">
            <div>
                <a href="{{ route('admin.jobs.show', $job) }}" class="fw-bold text-decoration-none">{{ $job->title }}</a>
                <div class="small text-muted-emp">{{ $job->category }} · {{ $job->location }} · {{ $job->created_at?->format('j M Y') }}</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-muted-emp">{{ number_format($job->applications_count) }} applications</span>
                <span class="status {{ $job->status === 'open' ? 'offer' : 'closed' }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
            </div>
        </div>
    @empty
        <p class="text-muted-emp mb-0">This company has not posted any jobs.</p>
    @endforelse
    @if ($jobs->hasPages())
        <div class="pt-3">{{ $jobs->links() }}</div>
    @endif
    </div>
</section>

<section class="card-emp mb-4">
    <div class="card-head"><h2 class="h6 mb-0">Company and job reports</h2><span class="small text-muted-emp">{{ number_format($reportCounts['total']) }} total · {{ number_format($reportCounts['open']) }} open</span></div>
    <div class="p-4">
@forelse ($reports as $report)
        <article class="border-top py-3">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div class="fw-bold">{{ ucfirst($report->target_type) }} · {{ $report->target_label }}</div>
                <span class="status {{ $report->status === 'open' ? 'interview' : 'closed' }}">{{ ucfirst($report->status) }}</span>
            </div>
            <div class="small text-muted-emp mt-1">{{ $report->reason }} · {{ ucfirst($report->severity) }} severity · {{ $report->created_at?->format('j M Y, H:i') }}</div>
            <p class="mb-1 mt-2 text-break">{{ $report->details }}</p>
            <div class="small text-muted-emp">Reported by {{ $report->reporter_name ?: 'Anonymous' }}{{ $report->reporter_email ? ' · '.$report->reporter_email : '' }}</div>
        </article>
    @empty
        <p class="text-muted-emp mb-0">No reports are associated with this company or its jobs.</p>
    @endforelse
    @if ($reports->hasPages())
        <div class="pt-3">{{ $reports->links() }}</div>
    @endif
    </div>
</section>

@if ($activities->isNotEmpty())
    <section class="card-emp mb-4">
    <div class="card-head"><h2 class="h6 mb-0">Recent administration activity</h2></div>
    <div class="p-4">
@foreach ($activities as $activity)
            <div class="border-top py-3">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <span class="fw-bold">{{ str_replace('.', ' · ', ucfirst($activity->action)) }}</span>
                    <span class="small text-muted-emp">{{ $activity->created_at?->format('j M Y, H:i') }}</span>
                </div>
                <div class="small text-muted-emp">{{ $activity->admin?->name ?? 'Former administrator' }}{{ $activity->details ? ' · '.$activity->details : '' }}</div>
            </div>
        @endforeach
    </div>
</section>
@endif

<section class="card-emp border-danger p-4">
    <h2 class="h6 text-danger mb-2"><i class="bi bi-exclamation-octagon me-2"></i>Permanently delete company</h2>
    <p class="mb-3">This permanently removes the company profile and logo, all its job listings, every application to those jobs, related reports, and identifying company/job audit entries. Candidate CVs remain in their owners’ accounts. This cannot be undone.</p>
    <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" onsubmit="return confirm('Permanently delete this company, its jobs, applications, and related reports? This cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete company and related data</button>
    </form>
</section>
@endsection
