@extends('layouts.admin')
@section('title', 'Jobs')

@section('content')
@php $status = request('status', 'all'); @endphp
<div class="page-head mb-4"><h1 class="page-title">Jobs</h1><p class="text-muted-emp mb-0">Keep the job board honest and safe.</p></div>

<div class="card-emp p-3 mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <ul class="nav nav-pills-emp mb-0 flex-nowrap overflow-auto">
        @foreach (['all' => 'All', 'Open' => 'Open', 'Flagged' => 'Flagged', 'Closed' => 'Closed', 'Taken down' => 'Taken down', 'Draft' => 'Draft'] as $k => $l)
            <li class="nav-item"><a class="nav-link {{ $status === $k ? 'active' : '' }}" href="{{ route('admin.jobs', ['status' => $k]) }}">{{ $l }}</a></li>
        @endforeach
    </ul>
    <form method="GET" action="{{ route('admin.jobs') }}" class="d-flex gap-2" role="search">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="input-icon" style="min-width:240px;"><i class="bi bi-search"></i><label class="visually-hidden" for="jq">Search jobs</label><input id="jq" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search title or company"></div>
        <button type="submit" class="btn btn-outline-navy btn-sm">Search</button>
    </form>
</div>

<div class="card-emp overflow-hidden">
    <div class="table-responsive">
    <table class="table table-emp align-middle">
        <thead><tr><th class="ps-4">Job</th><th>Status</th><th>Applicants</th><th class="d-none d-md-table-cell">Posted</th><th></th></tr></thead>
        <tbody>
        @forelse ($jobs as $j)
            <tr class="{{ $j['flag'] ? 'table-warning' : '' }}">
                <td class="ps-4"><div class="fw-bold text-navy">{{ $j['title'] }}</div><div class="small text-muted-emp">{{ $j['company'] }}</div>
                    @if ($j['flag'])<div class="small text-danger fw-bold"><i class="bi bi-flag-fill me-1"></i>{{ $j['flag'] }}</div>@endif</td>
                <td><span class="status {{ $j['status'] === 'Open' && $j['flag'] ? 'interview' : ($j['status'] === 'Open' ? 'offer' : 'submitted') }}">{{ $j['status'] === 'Open' && $j['flag'] ? 'Flagged' : $j['status'] }}</span></td>
                <td class="fw-bold">{{ number_format($j['applicants']) }}</td>
                <td class="small d-none d-md-table-cell">{{ $j['posted'] }}</td>
                <td class="text-end pe-3">
                    <a href="{{ route('admin.jobs.show', $j['id']) }}" class="btn btn-outline-navy btn-sm me-1">View</a>
                    @if ($j['flag'])
                        @if ($j['status'] === 'Open')
                            <form class="d-inline" method="POST" action="{{ route('admin.jobs.take-down', $j['id']) }}">@csrf<button class="btn btn-navy btn-sm me-1">Take down</button></form>
                        @elseif ($j['status'] === 'Taken down')
                            <form class="d-inline" method="POST" action="{{ route('admin.jobs.keep-live', $j['id']) }}">@csrf<button class="btn btn-outline-navy btn-sm">Keep live</button></form>
                        @endif
                    @else
                        <div class="dropdown d-inline-block"><button class="icon-btn" data-bs-toggle="dropdown" aria-label="Job actions"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="mailto:{{ $j['owner_email'] }}">Contact company</a></li>
                                @if ($j['status'] === 'Open')
                                    <li><form method="POST" action="{{ route('admin.jobs.take-down', $j['id']) }}">@csrf<button class="dropdown-item text-danger" type="submit">Take down</button></form></li>
                                @elseif ($j['status'] === 'Taken down')
                                    <li><form method="POST" action="{{ route('admin.jobs.keep-live', $j['id']) }}">@csrf<button class="dropdown-item" type="submit">Keep live</button></form></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-0">@include('components.empty-state', ['icon' => 'briefcase', 'title' => 'No jobs found', 'text' => 'Try a different filter.'])</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
