@extends('layouts.employer')
@section('title', 'My jobs')

@section('content')
<div class="page-banner">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div><h1 class="page-title">My jobs</h1><p class="mb-0">Create, edit and close your job posts.</p></div>
        <div class="d-flex flex-wrap gap-2"><a href="{{ route('employer.jobs.create') }}" class="btn btn-emp"><i class="bi bi-plus-lg me-1"></i>Post a job</a></div>
    </div>
</div>

<div class="card-emp overflow-visible">
    @forelse ($jobs as $j)
        <div class="p-3 p-md-4 d-flex flex-wrap align-items-center gap-3" style="{{ $loop->first ? '' : 'border-top:1px solid var(--emp-line);' }}">
            <div class="icon-tile d-none d-sm-flex"><i class="bi bi-briefcase"></i></div>
            <div class="flex-grow-1 min-w-0" style="flex-basis:220px;">
                <a href="{{ route('employer.jobs.show', $j['id']) }}" class="fw-bold text-navy text-decoration-none">{{ $j['title'] }}</a>
                <div class="small text-muted-emp">{{ $j['location'] }} · {{ $j['mode'] }} · {{ $j['type'] }} · Posted {{ $j['posted'] }}</div>
            </div>
            <div class="text-center px-2"><div class="fw-800 text-navy">{{ $j['applicants'] }}</div><div class="small text-muted-emp">Applicants</div></div>
            <div class="text-center px-2"><div class="fw-800 text-orange">{{ $j['new'] }}</div><div class="small text-muted-emp">New</div></div>
            <div class="text-center px-2 d-none d-md-block"><div class="fw-800 text-navy">{{ $j['views'] }}</div><div class="small text-muted-emp">Views</div></div>
            @include('employer.partials.job-status', ['status' => $j['status']])
            <div class="dropdown">
                <button class="icon-btn" data-bs-toggle="dropdown" aria-label="Job actions"><i class="bi bi-three-dots-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('employer.jobs.show', $j['id']) }}">View job details</a></li>
                    <li><form method="POST" action="{{ route('employer.jobs.duplicate', $j['id']) }}">@csrf<button class="dropdown-item" type="submit">Duplicate as draft</button></form></li>
                    @if ($j['status'] === 'Open')
                        <li><form method="POST" action="{{ route('employer.jobs.close', $j['id']) }}">@csrf<button class="dropdown-item text-danger" type="submit">Close job</button></form></li>
                    @endif
                </ul>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-briefcase"></i>
            <h2 class="h5 text-navy mt-2">No jobs yet</h2>
            <p class="small text-muted-emp">Post your first job to start receiving applications.</p>
            <a href="{{ route('employer.jobs.create') }}" class="btn btn-emp btn-sm">Post a job</a>
        </div>
    @endforelse
</div>
@endsection
