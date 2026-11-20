@extends('layouts.admin')
@section('title', 'Companies')

@section('content')
@php $status = request('status', 'all'); @endphp
<div class="page-head mb-4"><h1 class="page-title">Companies</h1><p class="text-muted-emp mb-0">Verify company accounts before their jobs go live. This protects candidates from scams.</p></div>

<ul class="nav nav-pills-emp mb-3 flex-nowrap overflow-auto">
    @foreach (['all' => 'All', 'Pending' => 'Pending', 'Verified' => 'Verified', 'Rejected' => 'Rejected'] as $k => $l)
        <li class="nav-item"><a class="nav-link {{ $status === $k ? 'active' : '' }}" href="{{ route('admin.companies', ['status' => $k]) }}">{{ $l }}</a></li>
    @endforeach
</ul>

@forelse ($companies as $c)
    <div class="card-emp p-3 p-md-4 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-3">
            @if ($c['logo_url'])
                <img src="{{ $c['logo_url'] }}" alt="{{ $c['name'] }} logo" width="48" height="48" class="rounded object-fit-cover">
            @else
                <div class="co-logo" style="background: {{ $c['color'] }}" aria-hidden="true">{{ $c['initials'] }}</div>
            @endif
            <div class="flex-grow-1 min-w-0"><div class="fw-bold text-navy">{{ $c['name'] }}</div><div class="small text-muted-emp">{{ $c['industry'] }} · Contact: {{ $c['owner'] }} · Registered {{ $c['registered'] }}@if($c['jobs']) · {{ $c['jobs'] }} live jobs @endif</div></div>
            <span class="status {{ $c['status'] === 'Verified' ? 'offer' : ($c['status'] === 'Pending' ? 'interview' : 'closed') }}">{{ $c['status'] }}</span>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.companies.show', $c['id']) }}" class="btn btn-outline-navy btn-sm">View</a>
                @if ($c['status'] === 'Pending')
                    <form method="POST" action="{{ route('admin.companies.verify', $c['id']) }}">
                        @csrf
                        <button class="btn btn-emp btn-sm" type="submit"><i class="bi bi-check-lg me-1"></i>Verify</button>
                    </form>
                    <form method="POST" action="{{ route('admin.companies.reject', $c['id']) }}">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-x-lg me-1"></i>Reject</button>
                    </form>
                @endif
            </div>
        </div>
        @if ($c['checks'])
            <div class="mt-3 pt-3" style="border-top:1px dashed var(--emp-line);">
                <div class="small fw-bold text-navy mb-2">Verification checks</div>
                <ul class="checklist">
                    @foreach ($c['checks'] as [$label, $ok])
                        <li class="{{ $ok ? 'ok' : 'warn' }}"><i class="bi bi-{{ $ok ? 'check-circle-fill' : 'exclamation-triangle-fill' }}"></i>{{ $label }}</li>
                    @endforeach
                </ul>
                @if (collect($c['checks'])->where(1, false)->count())<div class="small text-danger mt-2">Some checks failed. Consider asking the company for more information before verifying.</div>@endif
            </div>
        @endif
    </div>
@empty
    @include('components.empty-state', ['icon' => 'building', 'title' => 'No companies here', 'text' => 'Nothing matches this filter.'])
@endforelse
@endsection
