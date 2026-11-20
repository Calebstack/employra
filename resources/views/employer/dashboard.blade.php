@extends('layouts.employer')
@section('title', 'Dashboard')

@section('content')
<div class="page-head d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="page-title">Hello, {{ $employer['first_name'] }} 👋</h1>
        <p class="text-muted-emp mb-0">Here is where your hiring stands today.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach ([['briefcase', 'Open jobs', $stats['open'], ''], ['people', 'Total applicants', $stats['applicants'], ''], ['person-plus', 'New to review', $stats['new'], 'orange'], ['calendar-check', 'Interviews this week', $stats['interviews'], 'green']] as [$icon, $label, $num, $tone])
        <div class="col-6 col-lg-3"><div class="card-emp stat-card"><div class="icon-tile {{ $tone }}"><i class="bi bi-{{ $icon }}"></i></div><div><div class="num">{{ $num }}</div><div class="lbl">{{ $label }}</div></div></div></div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-emp p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Recent applications</h2></div>
            @forelse (array_slice($applicants, 0, 4) as $a)
                <a href="{{ route('employer.applicants.show', $a['id']) }}" class="d-flex align-items-center gap-3 py-3 text-decoration-none" style="border-top:1px solid var(--emp-line);">
                    <div class="avatar">{{ \App\Support\JobPresenter::initials($a['name']) }}</div>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold text-navy text-truncate">{{ $a['name'] }}</div><div class="small text-muted-emp text-truncate">{{ $a['job_title'] }} · Applied {{ $a['applied'] }}</div></div>
                    @include('employer.partials.app-status', ['status' => $a['status']])
                </a>
            @empty
                <p class="small text-muted-emp mb-0">Applicants will appear here after you post a job.</p>
            @endforelse
        </div>

        <div class="card-emp p-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Your jobs</h2><a href="{{ route('employer.jobs') }}" class="small fw-bold">Manage</a></div>
            @forelse (array_slice($jobs, 0, 3) as $j)
                <a href="{{ route('employer.jobs.show', $j['id']) }}" class="d-flex align-items-center gap-3 py-3 text-decoration-none" style="border-top:1px solid var(--emp-line);">
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold text-navy text-truncate">{{ $j['title'] }}</div><div class="small text-muted-emp">{{ $j['applicants'] }} applicants · closes {{ $j['deadline'] }}</div></div>
                    @include('employer.partials.job-status', ['status' => $j['status']])
                </a>
            @empty
                <div class="small text-muted-emp">No jobs yet. Post a job to start receiving applications.</div>
                <a href="{{ route('employer.jobs.create') }}" class="btn btn-emp btn-sm mt-3">Post your first job</a>
            @endforelse
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-emp p-4 mb-4">
            <h2 class="h6 mb-3">Hiring pipeline</h2>
            @foreach ($pipeline as $label => $n)
                <div class="d-flex justify-content-between small fw-bold mb-1"><span>{{ $label }}</span><span>{{ $n }}</span></div>
                <div class="progress-emp mb-3"><span style="width: {{ min(100, $n * 12) }}%"></span></div>
            @endforeach
        </div>
        <div class="card-emp p-4">
            <h2 class="h6 mb-3">Upcoming interviews</h2>
            @forelse (array_slice(array_filter($interviews, fn ($i) => $i['upcoming']), 0, 3) as $i)
                <div class="d-flex gap-3 mb-3">
                    <div class="avatar" style="width:36px;height:36px;font-size:.75rem;">{{ $i['initials'] }}</div>
                    <div class="small"><div class="fw-bold text-navy">{{ $i['name'] }}</div><div class="text-muted-emp">{{ $i['date'] }} · {{ $i['time'] }}</div></div>
                </div>
            @empty
                <p class="small text-muted-emp">No upcoming interviews.</p>
            @endforelse
            <a href="{{ route('employer.interviews') }}" class="btn btn-outline-navy btn-sm w-100">View all</a>
        </div>
    </div>
</div>
@endsection
