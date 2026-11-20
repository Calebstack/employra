@php $on = fn (string $pattern) => request()->routeIs($pattern) ? 'active' : ''; @endphp

<a href="{{ route('home') }}" class="brand" aria-label="Employra home">
    <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
</a>
<div class="px-2 pb-2"><span class="badge-soft orange"><i class="bi bi-shield-lock me-1"></i>Admin area</span></div>

<nav>
    <div class="side-label">Overview</div>
    <a class="side-link {{ $on('admin.dashboard') }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a>

    <div class="side-label">Manage</div>
    <a class="side-link {{ $on('admin.users') }}" href="{{ route('admin.users') }}"><i class="bi bi-people"></i>Users</a>
    <a class="side-link {{ $on('admin.companies') }}" href="{{ route('admin.companies') }}"><i class="bi bi-building-check"></i>Companies<span class="count">{{ $sidebarCounts['pending'] ?? 0 }}</span></a>
    <a class="side-link {{ $on('admin.applications') }}" href="{{ route('admin.applications') }}"><i class="bi bi-send"></i>Applications<span class="count">{{ $sidebarCounts['applications'] ?? 0 }}</span></a>
    <a class="side-link {{ $on('admin.jobs') }}" href="{{ route('admin.jobs') }}"><i class="bi bi-briefcase"></i>Jobs</a>
    <a class="side-link {{ $on('admin.reports') }}" href="{{ route('admin.reports') }}"><i class="bi bi-flag"></i>Reports<span class="count">{{ $sidebarCounts['reports'] ?? 0 }}</span></a>

    <div class="side-label">Platform</div>
    <a class="side-link {{ $on('admin.audit') }}" href="{{ route('admin.audit') }}"><i class="bi bi-clipboard-data"></i>Admin activity</a>
    <a class="side-link {{ $on('admin.settings') }}" href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i>Settings</a>
</nav>
