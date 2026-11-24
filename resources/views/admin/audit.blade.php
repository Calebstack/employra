@extends('layouts.admin')
@section('title', 'Admin activity')

@section('content')
@php $type = request('type', 'all'); $range = request('range', '30'); @endphp
<div class="page-banner slate compact d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div><h1 class="page-title h3">Admin activity</h1><p>A record of company, job, report, user, and settings actions made by administrators.</p></div>
    <div class="d-flex flex-wrap gap-2"><a href="{{ route('admin.audit.export', request()->query()) }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-download me-1"></i>Export log</a></div>
</div>

<div class="row g-3 mb-4">
    @foreach ([['activity', 'Recorded actions', number_format($auditStats['actions']), ''], ['calendar-week', 'Last 7 days', $auditStats['week'], 'green'], ['flag', 'Reports actioned', $auditStats['reports'], 'orange'], ['briefcase', 'Jobs taken down', $auditStats['jobs'], '']] as [$icon, $label, $num, $tone])
        <div class="col-6 col-lg-3"><div class="card-emp stat-card"><div class="icon-tile {{ $tone }}"><i class="bi bi-{{ $icon }}"></i></div><div><div class="num">{{ $num }}</div><div class="lbl">{{ $label }}</div></div></div></div>
    @endforeach
</div>

<div class="alert alert-info small mb-3"><i class="bi bi-shield-check me-1"></i>This log contains only actions recorded after admin activity logging was enabled; it does not invent or display sample AI decisions.</div>

<div class="card-emp p-3 mb-3 d-flex flex-wrap gap-3 align-items-center justify-content-between">
    <ul class="nav nav-pills-emp mb-0 flex-nowrap overflow-auto">
        @foreach (['all' => 'Everything', 'companies' => 'Companies', 'jobs' => 'Jobs', 'reports' => 'Reports', 'users' => 'Users', 'settings' => 'Settings', 'admins' => 'Admins'] as $key => $label)
            <li class="nav-item"><a class="nav-link {{ $type === $key ? 'active' : '' }}" href="{{ route('admin.audit', ['type' => $key, 'range' => $range]) }}">{{ $label }}</a></li>
        @endforeach
    </ul>
    <form method="GET" action="{{ route('admin.audit') }}" class="d-flex gap-2">
        <input type="hidden" name="type" value="{{ $type }}">
        <select name="range" class="form-select" aria-label="Date range" onchange="this.form.submit()">
            @foreach (['7' => 'Last 7 days', '30' => 'Last 30 days', '365' => 'Last year'] as $value => $label)
                <option value="{{ $value }}" @selected($range === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <noscript><button class="btn btn-outline-navy btn-sm">Apply</button></noscript>
    </form>
</div>

<div class="card-emp overflow-hidden">
    <div class="table-responsive">
    <table class="table table-emp align-middle mb-0">
        <thead><tr><th class="ps-4">When</th><th>Admin</th><th>Action</th><th>Target</th><th>Details</th></tr></thead>
        <tbody>
        @forelse ($audit as $a)
            <tr>
                <td class="ps-4 small text-nowrap">{{ $a['when'] }}</td>
                <td class="small fw-bold text-navy">{{ $a['who'] }}</td>
                <td class="small fw-bold">{{ $a['action'] }}</td>
                <td class="small">{{ $a['target'] }}</td>
                <td class="small">{{ $a['details'] }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-0">@include('components.empty-state', ['icon' => 'activity', 'title' => 'No recorded actions', 'text' => 'No admin activity matches this filter and date range.'])</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
