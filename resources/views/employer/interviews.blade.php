@extends('layouts.employer')
@section('title', 'Interviews')

@section('content')
<div class="page-banner">
    <h1 class="page-title">Interviews</h1>
    <p class="mb-0">Scheduled interviews and candidate responses across your jobs.</p>
</div>

<h2 class="h6 text-muted-emp text-uppercase mb-3"><i class="bi bi-calendar-event me-1"></i>Upcoming</h2>
@forelse (array_filter($interviews, fn ($interview) => $interview['upcoming']) as $interview)
    <div class="card-emp p-3 p-md-4 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="text-center px-3 py-2 rounded-3" style="background:var(--emp-orange-100); min-width:110px; border-left:4px solid var(--emp-orange);">
                <div class="small fw-bold text-orange">{{ $interview['date'] }}</div>
                <div class="fw-800 text-navy">{{ $interview['time'] }}</div>
            </div>
            <div class="avatar">{{ $interview['initials'] }}</div>
            <div class="flex-grow-1 min-w-0">
                <a href="{{ route('employer.applicants.show', $interview['applicant_id']) }}" class="fw-bold text-navy text-decoration-none">{{ $interview['name'] }}</a>
                <div class="small text-muted-emp">{{ $interview['job'] }} · {{ $interview['mode'] }}</div>
                @if ($interview['details'])<div class="small text-muted-emp">{{ $interview['details'] }}</div>@endif
                @if ($interview['response_note'])<div class="small mt-1">Candidate note: {{ $interview['response_note'] }}</div>@endif
            </div>
            <span class="status {{ $interview['status'] === 'Confirmed' ? 'offer' : 'interview' }}">{{ $interview['status'] }}</span>
        </div>
    </div>
@empty
    <div class="card-emp empty-state mb-4"><i class="bi bi-calendar-check"></i><p class="text-muted-emp mt-2 mb-0">No upcoming interviews are scheduled.</p></div>
@endforelse

<h2 class="h6 text-muted-emp text-uppercase mt-4 mb-3"><i class="bi bi-clock-history me-1"></i>Past</h2>
@forelse (array_filter($interviews, fn ($interview) => ! $interview['upcoming']) as $interview)
    <div class="card-emp p-3 mb-3 d-flex flex-wrap align-items-center gap-3">
        <div class="avatar">{{ $interview['initials'] }}</div>
        <div class="flex-grow-1">
            <a href="{{ route('employer.applicants.show', $interview['applicant_id']) }}" class="fw-bold text-navy text-decoration-none">{{ $interview['name'] }}</a>
            <div class="small text-muted-emp">{{ $interview['job'] }} · {{ $interview['date'] }} · {{ $interview['time'] }}</div>
        </div>
        <span class="status submitted">{{ $interview['status'] }}</span>
    </div>
@empty
    <div class="card-emp empty-state"><i class="bi bi-clock-history"></i><p class="text-muted-emp mt-2 mb-0">No past interviews.</p></div>
@endforelse
@endsection
