@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $upcoming = collect($applications)->first(fn ($application) => $application['interview'] && $application['interview']['respondable']);
@endphp
<div class="page-head d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="page-title">Welcome back, {{ $candidate['first_name'] }} 👋</h1>
        <p class="text-muted-emp mb-0">Here is what is happening with your job search today.</p>
    </div>
    <a href="{{ route('jobs.index') }}" class="btn btn-emp"><i class="bi bi-search me-1"></i>Find jobs</a>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['send', 'Applications', count($applications), ''],
        ['calendar-check', 'Upcoming interviews', collect($applications)->filter(fn ($application) => $application['interview'] && $application['interview']['respondable'])->count(), 'orange'],
        ['briefcase', 'Open jobs to explore', count($recentJobs), 'green'],
        ['file-earmark-text', 'CVs uploaded', count($cvs), ''],
    ] as [$icon, $label, $num, $tone])
        <div class="col-6 col-lg-3">
            <div class="card-emp stat-card">
                <div class="icon-tile {{ $tone }}"><i class="bi bi-{{ $icon }}"></i></div>
                <div><div class="num">{{ $num }}</div><div class="lbl">{{ $label }}</div></div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-8">
        @if ($upcoming)
            <div class="card-emp p-4 mb-4" style="border-left: 4px solid var(--emp-orange);">
                <div class="d-flex flex-wrap justify-content-between gap-3">
                    <div>
                        <span class="badge-soft orange mb-2"><i class="bi bi-calendar-check me-1"></i>Upcoming interview</span>
                        <h2 class="h5 mb-1">{{ $upcoming['job_title'] }} · {{ $upcoming['company'] }}</h2>
                        <div class="text-muted-emp">{{ $upcoming['interview']['date'] }} · {{ $upcoming['interview']['time'] }} · {{ $upcoming['interview']['mode'] }}</div>
                    </div>
                    <a href="{{ route('candidate.applications.show', $upcoming['id']) }}" class="btn btn-navy align-self-center">View details</a>
                </div>
            </div>
        @endif

        <div class="card-emp p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Recent applications</h2>
                <a href="{{ route('candidate.applications') }}" class="small fw-bold">View all</a>
            </div>
            @forelse (array_slice($applications, 0, 4) as $a)
                <a href="{{ route('candidate.applications.show', $a['id']) }}" class="d-flex align-items-center gap-3 py-3 text-decoration-none" style="border-top:1px solid var(--emp-line);">
                    <div class="co-logo" style="background: {{ $a['company_color'] }}" aria-hidden="true">{{ $a['company_initials'] }}</div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-navy text-truncate">{{ $a['job_title'] }}</div>
                        <div class="small text-muted-emp">{{ $a['company'] }} · Applied {{ $a['submitted'] }}</div>
                    </div>
                    @include('components.status-badge', ['status' => $a['status']])
                </a>
            @empty
                <p class="small text-muted-emp mb-0">Your submitted applications and their progress will appear here.</p>
            @endforelse
        </div>

        <div class="card-emp p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Recently posted jobs</h2>
                <a href="{{ route('candidate.recommended') }}" class="small fw-bold">Browse more</a>
            </div>
            @forelse (array_slice($recentJobs, 0, 3) as $j)
                <a href="{{ route('jobs.show', $j['slug']) }}" class="d-flex align-items-center gap-3 py-3 text-decoration-none" style="border-top:1px solid var(--emp-line);">
                    <div class="co-logo" style="background: {{ $j['company_color'] }}" aria-hidden="true">{{ $j['company_initials'] }}</div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-navy text-truncate">{{ $j['title'] }}</div>
                        <div class="small text-muted-emp">{{ $j['company'] }} · {{ $j['location'] }}</div>
                    </div>
                </a>
            @empty
                <p class="small text-muted-emp mb-0">No additional open jobs are available right now.</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-emp p-4 mb-4">
            <h2 class="h6 mb-3">Profile strength</h2>
            <div class="d-flex justify-content-between small fw-bold mb-1"><span>{{ $candidate['completion'] }}% complete</span><span class="text-orange">Profile strength</span></div>
            <div class="progress-emp mb-3"><span style="width: {{ $candidate['completion'] }}%"></span></div>
            <ul class="list-unstyled small mb-3">
                @foreach ($candidate['completion_missing'] as $m)
                    <li class="mb-1"><i class="bi bi-plus-circle text-orange me-1"></i>{{ $m }}</li>
                @endforeach
            </ul>
            <a href="{{ route('candidate.profile') }}" class="btn btn-outline-navy btn-sm w-100">Complete profile</a>
        </div>

        <div class="card-emp p-4 mb-4">
            <h2 class="h6 mb-3">Your saved CVs</h2>
            @forelse (array_slice($cvs, 0, 3) as $cv)
                <div class="small d-flex justify-content-between gap-2 mb-2"><span class="text-truncate">{{ $cv['filename'] }}</span><a href="{{ $cv['download_url'] }}">Download</a></div>
            @empty
                <p class="small text-muted-emp">Upload a CV once, then select it when applying.</p>
            @endforelse
            <a href="{{ route('candidate.cvs') }}" class="btn btn-outline-navy btn-sm w-100 mt-2">Manage CVs</a>
        </div>
    </div>
</div>
@endsection
