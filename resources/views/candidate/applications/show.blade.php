@extends('layouts.app')
@section('title', 'Application')

@section('content')
@php
    $closed = $application['is_closed'] ?? $application['status'] === 'Closed';
@endphp
<nav aria-label="breadcrumb"><ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="{{ route('candidate.applications') }}">My applications</a></li>
    <li class="breadcrumb-item active">{{ $application['job_title'] }}</li>
</ol></nav>

<div class="card-emp p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center gap-3">
        <div class="co-logo" style="background: {{ $application['company_color'] }}" aria-hidden="true">{{ $application['company_initials'] }}</div>
        <div class="flex-grow-1">
            <h1 class="h4 mb-1">{{ $application['job_title'] }}</h1>
            <div class="text-muted-emp">{{ $application['company'] }} · {{ $application['location'] }} · {{ $application['mode'] }} · {{ $application['type'] }}</div>
        </div>
        @include('components.status-badge', ['status' => $application['status']])
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        @if ($closed)
            <div class="alert alert-danger"><i class="bi bi-info-circle me-1"></i>{{ $application['closed_note'] }}</div>
        @endif

        @if ($application['interview'])
            @php $iv = $application['interview']; @endphp
            <div class="card-emp p-4 mb-4" style="border-left: 4px solid var(--emp-orange);">
                <h2 class="h5 mb-3"><i class="bi bi-calendar-check text-orange me-1"></i>Your interview</h2>
                <dl class="row mb-0 small">
                    <dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $iv['date'] }}</dd>
                    <dt class="col-sm-3">Time</dt><dd class="col-sm-9">{{ $iv['time'] }}</dd>
                    <dt class="col-sm-3">Format</dt><dd class="col-sm-9">{{ $iv['mode'] }}</dd>
                    <dt class="col-sm-3">Where</dt><dd class="col-sm-9">{{ $iv['where'] }}</dd>
                    <dt class="col-sm-3">You will meet</dt><dd class="col-sm-9">{{ $iv['with'] }}</dd>
                    <dt class="col-sm-3">Good to know</dt><dd class="col-sm-9">{{ $iv['notes'] }}</dd>
                </dl>
                @if ($iv['response'] === 'confirmed')
                    <p class="small text-success mt-3 mb-0">You confirmed this interview.</p>
                @elseif ($iv['response'] === 'reschedule_requested')
                    <p class="small text-warning-emphasis mt-3 mb-0">You requested a new time. The company will follow up.</p>
                @elseif ($iv['respondable'])
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <form method="POST" action="{{ route('candidate.interviews.response', $iv['id']) }}">
                            @csrf<input type="hidden" name="response" value="confirmed">
                            <button class="btn btn-emp btn-sm" type="submit">Confirm attendance</button>
                        </form>
                        <form method="POST" action="{{ route('candidate.interviews.response', $iv['id']) }}" class="d-flex gap-2">
                            @csrf<input type="hidden" name="response" value="reschedule_requested">
                            <input class="form-control form-control-sm" name="note" maxlength="1000" placeholder="Reason or preferred time (optional)" aria-label="Reschedule note">
                            <button class="btn btn-outline-navy btn-sm text-nowrap" type="submit">Ask to reschedule</button>
                        </form>
                    </div>
                @endif
                @if ($iv['response_note'])
                    <p class="small mt-2 mb-0">Your note: {{ $iv['response_note'] }}</p>
                @endif
            </div>
        @endif

        <div class="card-emp p-4">
            <h2 class="h5 mb-4">Progress</h2>
            <ul class="timeline">
                @foreach ($application['timeline'] as $i => $t)
                    @php $state = $t['current'] ? 'current' : 'done'; @endphp
                    <li class="{{ $state }}">
                        <span class="node"><i class="bi bi-{{ $state === 'done' ? 'check-lg' : ($state === 'current' ? 'circle-fill' : 'circle') }}"></i></span>
                        <div class="t-title">{{ $t['stage'] }}</div>
                        @if ($t['date'])<div class="t-date">{{ $t['date'] }}</div>@endif
                        @if ($t['note'])<div class="small">{{ $t['note'] }}</div>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-emp p-4 mb-4">
            <h2 class="h6 mb-3">Application details</h2>
            <dl class="mb-0 small">
                <dt>Submitted</dt><dd>{{ $application['submitted'] }}</dd>
                <dt>CV used</dt><dd><i class="bi bi-file-earmark-pdf me-1"></i>{{ $application['cv'] }}</dd>
                <dt>Salary range</dt><dd>{{ $application['salary'] }}</dd>
                @if ($application['match'] !== null)
                    <dt>Your match</dt><dd>@include('components.match-pill', ['match' => $application['match']])</dd>
                @else
                    <dt>Match review</dt><dd>Not yet scored</dd>
                @endif
            </dl>
        </div>
        <div class="card-emp p-4">
            <h2 class="h6 mb-3">Actions</h2>
            <a href="{{ route('jobs.show', $application['job_slug']) }}" class="btn btn-outline-navy w-100 mb-2">View job post</a>
            @unless ($closed)
                <button class="btn btn-ghost w-100 text-danger" type="button" data-bs-toggle="modal" data-bs-target="#withdrawModal">Withdraw application</button>
            @endunless
        </div>
    </div>
</div>

<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border-radius:16px;">
        <div class="modal-body p-4">
            <h2 class="h5">Withdraw this application?</h2>
            <p class="text-muted-emp">{{ $application['company'] }} will be told you are no longer interested. You can apply again later if the job is still open.</p>
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-outline-navy" data-bs-dismiss="modal">Keep application</button>
                <form method="POST" action="{{ route('candidate.applications.withdraw', $application['id']) }}">
                    @csrf
                    <button class="btn btn-navy" type="submit">Yes, withdraw</button>
                </form>
            </div>
        </div>
    </div></div>
</div>
@endsection
