@extends('layouts.employer')
@section('title', 'Dashboard')

@section('content')
<div class="page-banner dashboard-banner employer-dashboard-banner">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="page-title">Hello, {{ $employer['first_name'] }}</h1>
            <p class="mb-0">Here is where your hiring stands today.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('employer.jobs.create') }}" class="btn btn-emp"><i class="bi bi-plus-lg me-1"></i>Post a job</a>
            <a href="{{ route('employer.jobs') }}" class="btn btn-outline-navy"><i class="bi bi-briefcase me-1"></i>My jobs</a>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4 mb-4">
    @foreach ([['briefcase', 'Open jobs', $stats['open'], ''], ['people', 'Total applicants', $stats['applicants'], ''], ['person-plus', 'New to review', $stats['new'], 'orange'], ['calendar-check', 'Interviews this week', $stats['interviews'], 'green']] as [$icon, $label, $num, $tone])
        <div class="col-6 col-lg-3"><div class="card-emp stat-card d-flex align-items-center gap-3"><div class="icon-tile {{ $tone }}"><i class="bi bi-{{ $icon }}"></i></div><div class="min-w-0"><div class="num">{{ $num }}</div><div class="lbl">{{ $label }}</div></div></div></div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-emp mb-4">
            <div class="card-head"><h2 class="h5 mb-0">Recent applications</h2><i class="bi bi-people text-muted-emp" aria-hidden="true"></i></div>
            <div class="px-3 px-md-4">
            @forelse (array_slice($applicants, 0, 4) as $a)
                <a href="{{ route('employer.applicants.show', $a['id']) }}" class="d-flex align-items-center gap-3 py-3 text-decoration-none" style="{{ $loop->first ? '' : 'border-top:1px solid var(--emp-line);' }}">
                    <div class="avatar">{{ \App\Support\JobPresenter::initials($a['name']) }}</div>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold text-navy text-truncate">{{ $a['name'] }}</div><div class="small text-muted-emp text-truncate">{{ $a['job_title'] }} · Applied {{ $a['applied'] }}</div></div>
                    @include('employer.partials.app-status', ['status' => $a['status']])
                </a>
            @empty
                <div class="empty-state px-0"><i class="bi bi-people"></i><p class="small text-muted-emp mt-2 mb-0">Applicants will appear here after you post a job.</p></div>
            @endforelse
            </div>
        </div>

        <div class="card-emp">
            <div class="card-head"><h2 class="h5 mb-0">Your jobs</h2><a href="{{ route('employer.jobs') }}" class="small fw-bold">Manage</a></div>
            <div class="px-3 px-md-4">
            @forelse (array_slice($jobs, 0, 3) as $j)
                <a href="{{ route('employer.jobs.show', $j['id']) }}" class="d-flex align-items-center gap-3 py-3 text-decoration-none" style="{{ $loop->first ? '' : 'border-top:1px solid var(--emp-line);' }}">
                    <div class="icon-tile"><i class="bi bi-briefcase"></i></div>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold text-navy text-truncate">{{ $j['title'] }}</div><div class="small text-muted-emp">{{ $j['applicants'] }} applicants · closes {{ $j['deadline'] }}</div></div>
                    @include('employer.partials.job-status', ['status' => $j['status']])
                </a>
            @empty
                <div class="empty-state px-0">
                    <i class="bi bi-briefcase"></i>
                    <div class="small text-muted-emp mt-2">No jobs yet. Post a job to start receiving applications.</div>
                    <a href="{{ route('employer.jobs.create') }}" class="btn btn-emp btn-sm mt-3">Post your first job</a>
                </div>
            @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-emp mb-4">
            <div class="card-head"><h2 class="h6 mb-0">Hiring pipeline</h2><i class="bi bi-funnel text-muted-emp" aria-hidden="true"></i></div>
            <div class="p-4 pb-1">
            @foreach ($pipeline as $label => $n)
                <div class="d-flex justify-content-between small fw-bold mb-1"><span>{{ $label }}</span><span>{{ $n }}</span></div>
                <div class="progress-emp mb-3"><span style="width: {{ min(100, $n * 12) }}%"></span></div>
            @endforeach
            </div>
        </div>
        <div class="card-emp">
            <div class="card-head"><h2 class="h6 mb-0">Upcoming interviews</h2><i class="bi bi-calendar-check text-muted-emp" aria-hidden="true"></i></div>
            <div class="p-4">
            @forelse (array_slice(array_filter($interviews, fn ($i) => $i['upcoming']), 0, 3) as $i)
                <div class="d-flex align-items-center gap-3 mb-3">
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
</div>
@endsection
