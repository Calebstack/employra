@php
    $on = fn (string $pattern) => request()->routeIs($pattern) ? 'active' : '';
@endphp

<a href="{{ route('home') }}" class="brand" aria-label="Employra home">
    <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
</a>

<div class="d-flex align-items-center gap-2 px-2 pb-2">
    @if (!empty($employer['company']['logo_url']))
        <img src="{{ $employer['company']['logo_url'] }}" alt="{{ $employer['company']['name'] }} logo" width="38" height="38" class="rounded object-fit-cover">
    @else
        <div class="co-logo" style="background: {{ $employer['company']['color'] ?? '#1558d6' }}; width:38px; height:38px; font-size:.8rem;" aria-hidden="true">{{ $employer['company']['initials'] ?? 'BP' }}</div>
    @endif
    <div class="min-w-0"><div class="fw-bold text-navy small text-truncate">{{ $employer['company']['name'] ?? 'Your company' }}</div><div class="small text-muted-emp">Employer account</div></div>
</div>

<nav>
    <div class="side-label">Overview</div>
    <a class="side-link {{ $on('employer.dashboard') }}" href="{{ route('employer.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>

    <div class="side-label">Hiring</div>
    <a class="side-link {{ $on('employer.jobs*') }}" href="{{ route('employer.jobs') }}"><i class="bi bi-briefcase"></i>My jobs<span class="count">{{ $sidebarCounts['new'] ?? 8 }} new</span></a>
    <a class="side-link {{ $on('employer.interviews') }}" href="{{ route('employer.interviews') }}"><i class="bi bi-calendar-check"></i>Interviews</a>

    <div class="side-label">Company</div>
    <a class="side-link {{ $on('employer.company') }}" href="{{ route('employer.company') }}"><i class="bi bi-building"></i>Company profile</a>
</nav>

<div class="side-card mt-4">
    <div class="fw-800 mb-1"><i class="bi bi-plus-circle me-1"></i> Hiring someone?</div>
    <div class="small mb-2">Post a job in a few minutes and start receiving applicants.</div>
    <a href="{{ route('employer.jobs.create') }}" class="btn btn-emp btn-sm w-100">Post a job</a>
</div>
