@extends('layouts.app')
@section('title', 'My applications')

@section('content')
@php $stages = \App\Support\CandidateMockData::stages(); $filter = request('status', 'all'); @endphp
<div class="page-banner compact">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="page-title">My applications</h1>
            <p>Track every application and see what happens next.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-emp"><i class="bi bi-search me-1"></i>Find more jobs</a>
    </div>
</div>

<ul class="nav nav-pills-emp mb-3 flex-nowrap overflow-auto">
    @foreach (['all' => 'All', 'active' => 'In progress', 'interview' => 'Interviews', 'closed' => 'Closed'] as $k => $label)
        <li class="nav-item"><a class="nav-link {{ $filter === $k ? 'active' : '' }}" href="{{ route('candidate.applications', ['status' => $k]) }}">{{ $label }}</a></li>
    @endforeach
</ul>

@forelse ($applications as $a)
    @php $idx = array_search($a['status'], $stages); @endphp
    <a href="{{ route('candidate.applications.show', $a['id']) }}" class="card-emp card-hover p-3 p-md-4 mb-3 d-block text-decoration-none">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="co-logo" style="background: {{ $a['company_color'] }}" aria-hidden="true">{{ $a['company_initials'] }}</div>
            <div class="flex-grow-1 min-w-0">
                <div class="fw-bold text-navy">{{ $a['job_title'] }}</div>
                <div class="small text-muted-emp">{{ $a['company'] }} · {{ $a['location'] }} · Applied {{ $a['submitted'] }}</div>
            </div>
            <div class="text-md-end">
                @include('components.status-badge', ['status' => $a['status']])
                <div class="small text-muted-emp mt-1">Updated {{ $a['updated'] }}</div>
            </div>
        </div>
        <div class="d-flex justify-content-between small text-muted-emp mt-3 mb-1"><span>Progress</span><span>{{ $a['status'] }}</span></div>
        <div class="stage-track {{ $a['is_closed'] ? 'closed' : '' }}" aria-hidden="true">
            @foreach ($stages as $i => $s)
                <span class="{{ $a['is_closed'] ? ($i < 2 ? 'on' : '') : ($idx !== false && $i <= $idx ? 'on' : '') }}"></span>
            @endforeach
        </div>
    </a>
@empty
    @include('components.empty-state', ['icon' => 'kanban', 'title' => 'Nothing here yet', 'text' => 'No applications match this filter.', 'actionUrl' => route('jobs.index'), 'actionLabel' => 'Browse jobs'])
@endforelse
@endsection
