@extends('layouts.admin')
@section('title', 'Applications')

@section('content')
<div class="page-banner slate compact">
    <h1 class="page-title h3">Applications</h1>
    <p>Applications submitted by candidate users to company job listings.</p>
</div>

<div class="card-emp p-3 mb-3">
    <ul class="nav nav-pills-emp flex-nowrap overflow-auto mb-0">
        @foreach (['all' => 'All', 'submitted' => 'Submitted', 'reviewing' => 'Reviewing', 'shortlisted' => 'Shortlisted', 'interview' => 'Interview', 'offer' => 'Offer', 'rejected' => 'Rejected', 'withdrawn' => 'Withdrawn'] as $value => $label)
            <li class="nav-item"><a class="nav-link {{ $status === $value ? 'active' : '' }}" href="{{ route('admin.applications', ['status' => $value]) }}">{{ $label }}</a></li>
        @endforeach
    </ul>
</div>

<div class="card-emp overflow-hidden">
    <div class="table-responsive">
        <table class="table table-emp align-middle mb-0">
            <thead><tr><th class="ps-4">Candidate</th><th>Job</th><th>Company</th><th>Status</th><th>Submitted</th></tr></thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td class="ps-4"><a href="{{ route('admin.users.show', $application['candidate_id']) }}" class="fw-bold text-navy">{{ $application['candidate'] }}</a><div class="small text-muted-emp">{{ $application['email'] }}</div></td>
                        <td>{{ $application['job'] }}</td>
                        <td>{{ $application['company'] }}</td>
                        <td>@include('components.status-badge', ['status' => $application['status']])</td>
                        <td class="small">{{ $application['submitted'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-0">@include('components.empty-state', ['icon' => 'send', 'title' => 'No applications found', 'text' => 'Submitted applications will appear here.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
