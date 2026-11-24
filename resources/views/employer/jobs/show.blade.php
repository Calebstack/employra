@extends('layouts.employer')
@section('title', $job['title'])

@section('content')
@php $filter = request('stage', 'all'); @endphp
<div class="page-banner">
    <nav aria-label="breadcrumb"><ol class="breadcrumb small mb-2"><li class="breadcrumb-item"><a href="{{ route('employer.jobs') }}" class="text-white-50">My jobs</a></li><li class="breadcrumb-item active text-white" aria-current="page">{{ $job['title'] }}</li></ol></nav>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="page-title">{{ $job['title'] }} <span class="ms-1 align-middle fs-6">@include('employer.partials.job-status', ['status' => $job['status']])</span></h1>
            <p class="mb-0">{{ $job['location'] }} · {{ $job['type'] }} · Closes {{ $job['deadline'] }} · {{ $job['applicants'] }} applications</p>
        </div>
        @if ($job['status'] === 'Open')
            <div class="d-flex flex-wrap gap-2"><a href="{{ route('jobs.show', $job['slug']) }}" class="btn btn-outline-navy"><i class="bi bi-box-arrow-up-right me-1"></i>View public page</a></div>
        @endif
    </div>
</div>

<div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i>Applications are shown in submission order. Review each candidate's submitted details before making a decision.</div>

<div class="card-emp p-3 mb-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <ul class="nav nav-pills-emp flex-nowrap overflow-auto mb-0">
            @foreach (['all' => 'All', 'New' => 'New', 'Reviewing' => 'Reviewing', 'Shortlisted' => 'Shortlisted', 'Interview' => 'Interview', 'Rejected' => 'Rejected'] as $k => $label)
                <li class="nav-item"><a class="nav-link {{ $filter === $k ? 'active' : '' }}" href="{{ route('employer.jobs.show', [$job['id'], 'stage' => $k]) }}">{{ $label }}</a></li>
            @endforeach
        </ul>
    </div>
</div>

@forelse ($applicants as $a)
    <div class="card-emp p-3 p-md-4 mb-3">
        <div class="d-flex flex-wrap justify-content-between gap-3">
            <div class="d-flex gap-3 min-w-0">
                <div class="avatar">{{ \App\Support\JobPresenter::initials($a['name']) }}</div>
                <div class="min-w-0">
                <a href="{{ route('employer.applicants.show', $a['id']) }}" class="h5 text-navy text-decoration-none">{{ $a['name'] }}</a>
                <div class="small text-muted-emp">{{ $a['email'] }} · {{ $a['headline'] }} · {{ $a['location'] }}</div>
                <div class="small text-muted-emp mt-1">Applied {{ $a['applied'] }}</div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-start gap-2">@include('employer.partials.app-status', ['status' => $a['status']])<a href="{{ route('employer.applicants.show', $a['id']) }}" class="btn btn-outline-navy btn-sm">Review</a></div>
        </div>
        @if ($a['cover_note'])
            <p class="small mt-3 mb-0 p-3 rounded-3" style="background:#f5f7fb;">{{ $a['cover_note'] }}</p>
        @endif
    </div>
@empty
    @include('components.empty-state', ['icon' => 'people', 'title' => 'No applicants here', 'text' => 'No one is in this stage yet.'])
@endforelse
@endsection
