@extends('layouts.employer')
@section('title', $job['title'])

@section('content')
@php $filter = request('stage', 'all'); @endphp
<nav aria-label="breadcrumb"><ol class="breadcrumb small"><li class="breadcrumb-item"><a href="{{ route('employer.jobs') }}">My jobs</a></li><li class="breadcrumb-item active">{{ $job['title'] }}</li></ol></nav>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <h1 class="page-title">{{ $job['title'] }} <span class="ms-1 align-middle">@include('employer.partials.job-status', ['status' => $job['status']])</span></h1>
        <p class="text-muted-emp mb-0">{{ $job['location'] }} · {{ $job['type'] }} · Closes {{ $job['deadline'] }} · {{ $job['applicants'] }} applications</p>
    </div>
    @if ($job['status'] === 'Open')
        <a href="{{ route('jobs.show', $job['slug']) }}" class="btn btn-outline-navy btn-sm">View public page</a>
    @endif
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
            <div class="min-w-0">
                <a href="{{ route('employer.applicants.show', $a['id']) }}" class="h5 text-navy text-decoration-none">{{ $a['name'] }}</a>
                <div class="small text-muted-emp">{{ $a['email'] }} · {{ $a['headline'] }} · {{ $a['location'] }}</div>
                <div class="small text-muted-emp mt-1">Applied {{ $a['applied'] }}</div>
            </div>
            <div class="text-end">@include('employer.partials.app-status', ['status' => $a['status']])</div>
        </div>
        @if ($a['cover_note'])
            <p class="small mt-3 mb-0">{{ $a['cover_note'] }}</p>
        @endif
    </div>
@empty
    @include('components.empty-state', ['icon' => 'people', 'title' => 'No applicants here', 'text' => 'No one is in this stage yet.'])
@endforelse
@endsection
