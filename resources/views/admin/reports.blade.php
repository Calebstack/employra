@extends('layouts.admin')
@section('title', 'Reports')

@section('content')
@php $status = request('status', 'all'); @endphp
<div class="page-banner slate compact">
    <h1 class="page-title h3">Reports</h1>
    <p>Concerns raised by users and by our own checks. Always hear both sides before acting.</p>
</div>

<ul class="nav nav-pills-emp mb-3 flex-nowrap overflow-auto">
    @foreach (['all' => 'All', 'open' => 'Open', 'actioned' => 'Actioned', 'dismissed' => 'Dismissed'] as $k => $l)
        <li class="nav-item"><a class="nav-link {{ $status === $k ? 'active' : '' }}" href="{{ route('admin.reports', ['status' => $k]) }}">{{ $l }}</a></li>
    @endforeach
</ul>

@forelse ($reports as $r)
    <div class="card-emp p-3 p-md-4 mb-3" style="{{ $r['status'] === 'Open' && $r['severity'] === 'High' ? 'border-left:4px solid #e5484d;' : '' }}">
        <div class="d-flex flex-wrap justify-content-between gap-2">
            <div>
                <span class="badge-soft">{{ $r['type'] }}</span> <span class="sev {{ strtolower($r['severity']) }} ms-1">{{ $r['severity'] }}</span>
                <div class="fw-bold text-navy mt-2">{{ $r['target'] }}</div>
                <div class="small text-muted-emp">Reason: {{ $r['reason'] }} · Reported by {{ $r['by'] }} · {{ $r['when'] }}</div>
            </div>
            <span class="status align-self-start {{ $r['status'] === 'Open' ? 'interview' : ($r['status'] === 'Dismissed' ? 'closed' : 'offer') }}">{{ $r['status'] }}</span>
        </div>
        <div class="example-box">{{ $r['detail'] }}</div>
        @if ($r['status'] === 'Open')
            <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                <form method="POST" action="{{ route('admin.reports.action', $r['id']) }}" class="d-flex flex-wrap gap-2">
                    @csrf
                    <input type="text" name="resolution_note" class="form-control form-control-sm" maxlength="2000" placeholder="Action note (optional)" aria-label="Moderation action note">
                    <button class="btn btn-navy btn-sm" type="submit">Take action</button>
                </form>
                <form method="POST" action="{{ route('admin.reports.dismiss', $r['id']) }}">
                    @csrf
                    <button class="btn btn-outline-navy btn-sm" type="submit">Dismiss</button>
                </form>
                @if ($r['reporter_email'])
                    <form method="POST" action="{{ route('admin.reports.contact', $r['id']) }}" class="d-flex flex-wrap gap-2">
                        @csrf
                        <input type="text" name="message" class="form-control form-control-sm" minlength="5" maxlength="3000" required placeholder="Message to reporter" aria-label="Message to reporter">
                        <button class="btn btn-ghost btn-sm" type="submit"><i class="bi bi-chat-dots me-1"></i>Contact reporter</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
@empty
    @include('components.empty-state', ['icon' => 'flag', 'title' => 'No reports', 'text' => 'No user-submitted reports are waiting for review.'])
@endforelse
@endsection
