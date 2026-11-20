@php
    $on = fn (string $pattern) => request()->routeIs($pattern) ? 'active' : '';
@endphp

<a href="{{ route('home') }}" class="brand" aria-label="Employra home">
    <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
</a>

<nav>
    <div class="side-label">Overview</div>
    <a class="side-link {{ $on('candidate.dashboard') }}" href="{{ route('candidate.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>

    <div class="side-label">Job search</div>
    <a class="side-link {{ $on('candidate.recommended') }}" href="{{ route('candidate.recommended') }}"><i class="bi bi-bullseye"></i>Recommended jobs</a>
    <a class="side-link {{ $on('candidate.saved') }}" href="{{ route('candidate.saved') }}"><i class="bi bi-bookmark"></i>Saved jobs</a>
    <a class="side-link {{ $on('candidate.applications*') }}" href="{{ route('candidate.applications') }}"><i class="bi bi-kanban"></i>My applications<span class="count">{{ $sidebarCounts['applications'] ?? 4 }}</span></a>

    <div class="side-label">My profile</div>
    <a class="side-link {{ $on('candidate.cvs*') }}" href="{{ route('candidate.cvs') }}"><i class="bi bi-file-earmark-text"></i>My CVs &amp; analysis</a>
    <a class="side-link {{ $on('candidate.profile') }}" href="{{ route('candidate.profile') }}"><i class="bi bi-person"></i>Profile</a>
    <a class="side-link {{ $on('candidate.settings') }}" href="{{ route('candidate.settings') }}"><i class="bi bi-gear"></i>Settings</a>
</nav>

<div class="side-card mt-4">
    <div class="fw-800 mb-1"><i class="bi bi-stars me-1"></i> Boost your CV</div>
    <div class="small mb-2">Upload a CV and see clear suggestions to make it stronger.</div>
    <a href="{{ route('candidate.cvs') }}#upload" class="btn btn-emp btn-sm w-100">Upload CV</a>
</div>
