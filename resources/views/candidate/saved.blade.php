@extends('layouts.app')
@section('title', 'Saved jobs')

@section('content')
<div class="page-head mb-4">
    <h1 class="page-title">Saved jobs</h1>
    <p class="text-muted-emp mb-0">Job bookmarking is not available yet. You can apply directly to any open listing.</p>
</div>

@forelse ($jobs as $j)
    <div class="card-emp p-3 p-md-4 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="co-logo" style="background: {{ $j['company_color'] }}" aria-hidden="true">{{ $j['company_initials'] }}</div>
            <div class="flex-grow-1 min-w-0">
                <a href="{{ route('jobs.show', $j['slug']) }}" class="fw-bold text-navy text-decoration-none">{{ $j['title'] }}</a>
                <div class="small text-muted-emp">{{ $j['company'] }} · {{ $j['location'] }} · {{ $j['salary'] }}</div>
                <div class="small text-muted-emp">{{ $j['saved_on'] }} · Closes {{ $j['deadline'] }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('jobs.apply', $j['slug']) }}" class="btn btn-emp btn-sm">Apply</a>
                <button class="btn btn-outline-navy btn-sm" type="button"><i class="bi bi-trash me-1"></i>Remove</button>
            </div>
        </div>
    </div>
@empty
    @include('components.empty-state', ['icon' => 'bookmark', 'title' => 'No saved jobs', 'text' => 'Browse current openings and apply to a job to track it here.', 'actionUrl' => route('jobs.index'), 'actionLabel' => 'Browse jobs'])
@endforelse
@endsection
