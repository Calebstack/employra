@extends('layouts.app')
@section('title', 'Recommended jobs')

@section('content')
<div class="page-banner compact">
    <h1 class="page-title">Open jobs</h1>
    <p>Recently posted verified-company jobs you have not applied to yet.</p>
</div>

@forelse ($jobs as $j)
    <div class="card-emp job-card h-auto p-3 p-md-4 mb-3">
        <div class="d-flex flex-wrap gap-3">
            <div class="co-logo" style="background: {{ $j['company_color'] }}" aria-hidden="true">{{ $j['company_initials'] }}</div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        <a href="{{ route('jobs.show', $j['slug']) }}" class="h5 text-navy text-decoration-none">{{ $j['title'] }}</a>
                        <div class="small text-muted-emp">{{ $j['company'] }} · {{ $j['location'] }} · {{ $j['mode'] }} · {{ $j['type'] }}</div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('jobs.apply', $j['slug']) }}" class="btn btn-emp btn-sm">Apply</a>
                    <a href="{{ route('jobs.show', $j['slug']) }}" class="btn btn-outline-navy btn-sm">View job</a>
                </div>
            </div>
        </div>
    </div>
@empty
    @include('components.empty-state', ['icon' => 'briefcase', 'title' => 'No open jobs to show', 'text' => 'Verified companies have not posted additional open jobs yet.', 'actionUrl' => route('jobs.index'), 'actionLabel' => 'Browse all jobs'])
@endforelse
@endsection
