@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="page-banner slate dashboard-banner admin-dashboard-banner d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <h1 class="page-title">Platform overview</h1>
        <p>{{ now()->format('l, j F Y') }} · here is what needs you today.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <div class="dropdown"><button class="btn btn-outline-navy btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-calendar3 me-1"></i>{{ ['7' => 'Last 7 days', '30' => 'Last 30 days', '365' => 'This year'][$range] }}</button>
            <ul class="dropdown-menu dropdown-menu-end">
                @foreach (['7' => 'Last 7 days', '30' => 'Last 30 days', '365' => 'This year'] as $value => $label)
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['range' => $value]) }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="attention mb-4">
    <a href="{{ route('admin.companies', ['status' => 'Pending']) }}"><span class="n">{{ $stats['pending'] }}</span><span class="t">Companies waiting<br>for verification</span><i class="bi bi-chevron-right ms-auto text-muted-emp"></i></a>
    <a href="{{ route('admin.reports', ['status' => 'open']) }}"><span class="n">{{ $stats['reports'] }}</span><span class="t">Open reports<br>to review</span><i class="bi bi-chevron-right ms-auto text-muted-emp"></i></a>
    <a href="{{ route('admin.jobs', ['status' => 'Flagged']) }}"><span class="n">{{ $stats['flagged_jobs'] }}</span><span class="t">Flagged job posts<br>need a decision</span><i class="bi bi-chevron-right ms-auto text-muted-emp"></i></a>
</div>

<div class="row g-3 mb-4">
    @foreach ([['people', 'Users', number_format($stats['users']), ''], ['building-check', 'Companies', number_format($stats['companies']), 'orange'], ['person-gear', 'Admins', number_format($stats['admins']), ''], ['send', 'Applications', number_format($stats['applications']), 'green']] as [$icon, $label, $num, $tone])
        <div class="col-6 col-lg-3"><a href="{{ $label === 'Applications' ? route('admin.applications') : ($label === 'Companies' ? route('admin.companies') : ($label === 'Users' ? route('admin.users') : route('admin.settings'))) }}" class="card-emp stat-card text-decoration-none"><div class="icon-tile {{ $tone }}"><i class="bi bi-{{ $icon }}"></i></div>
            <div><div class="num">{{ $num }}</div><div class="lbl">{{ $label }}</div></div></a></div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-emp mb-4">
            <div class="card-head"><div><h2 class="h6 mb-0">Sign-ups · {{ $rangeLabel }}</h2></div><span class="badge-soft">{{ array_sum($signups) }} new accounts</span></div>
            <div class="p-4">
            @php $max = max(array_merge([1], array_values($signups))); @endphp
            <div class="d-flex align-items-end gap-2" style="height: 170px;" role="img" aria-label="Bar chart of sign-ups per day">
                @foreach ($signups as $day => $n)
                    <div class="flex-fill text-center">
                        <div class="small fw-bold text-navy">{{ $n }}</div>
                        <div style="height: {{ round($n / $max * 110) }}px; background: linear-gradient(180deg, {{ $n === $max ? 'var(--emp-orange), #ff9a4d' : 'var(--emp-blue), var(--emp-navy)' }}); border-radius: 8px 8px 0 0;"></div>
                        <div class="small text-muted-emp mt-1">{{ $day }}</div>
                    </div>
                @endforeach
            </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6"><div class="card-emp h-100">
                <div class="card-head"><h2 class="h6 mb-0">Applications</h2><a href="{{ route('admin.applications') }}" class="small fw-bold">View all</a></div>
                <div class="p-4">
                @php
                    $stageRows = collect($applicationStages)->all();
                    $stageTotal = array_sum($stageRows);
                    $stageColors = ['#1558d6', '#ff6a00', '#12a150', '#6a35d6', '#0a1f6e', '#98a2b3'];
                    $acc = 0; $stops = []; $i = 0;
                    foreach ($stageRows as $stage => $count) {
                        $from = $stageTotal ? $acc / $stageTotal * 100 : 0;
                        $acc += $count;
                        $to = $stageTotal ? $acc / $stageTotal * 100 : 0;
                        $stops[] = $stageColors[$i % count($stageColors)].' '.round($from, 2).'% '.round($to, 2).'%';
                        $i++;
                    }
                @endphp
                @if ($stageTotal > 0)
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="donut" style="background: conic-gradient({{ implode(', ', $stops) }});" role="img" aria-label="Applications by stage"><div class="donut-center"><div><strong class="text-navy fs-6">{{ number_format($stageTotal) }}</strong><div class="small text-muted-emp">total</div></div></div></div>
                        <div class="flex-grow-1 small">
                            @php $i = 0; @endphp
                            @foreach ($applicationStages as $stage => $count)
                                <div class="d-flex justify-content-between py-1"><span><span class="legend-dot" style="background: {{ $stageColors[$i % count($stageColors)] }}"></span>{{ $stage }}</span><strong>{{ $count }}</strong></div>
                                @php $i++; @endphp
                            @endforeach
                        </div>
                    </div>
                @else
                    @forelse ($applicationStages as $stage => $count)
                        <div class="d-flex justify-content-between small py-1"><span>{{ $stage }}</span><strong>{{ $count }}</strong></div>
                    @empty
                        <p class="small text-muted-emp mb-0">No applications have been submitted yet.</p>
                    @endforelse
                @endif
                </div>
            </div></div>
            <div class="col-md-6"><div class="card-emp h-100">
                <div class="card-head"><h2 class="h6 mb-0">Top job categories</h2></div>
                <div class="p-4">
                @if ($categories)
                    @php $ctop = max($categories); @endphp
                    @foreach ($categories as $label => $n)
                                <div class="bar-row"><span class="lbl text-truncate">{{ $label }}</span><span class="bar"><span style="width: {{ round($n / $ctop * 100) }}%"></span></span><span class="val">{{ $n }}</span></div>
                    @endforeach
                @else
                    <p class="small text-muted-emp mb-0">No employer jobs have been published yet.</p>
                @endif
                </div>
            </div></div>
        </div>

        <div class="card-emp">
            <div class="card-head"><h2 class="h6 mb-0">Companies waiting for verification</h2><a href="{{ route('admin.companies', ['status' => 'Pending']) }}" class="small fw-bold">See all</a></div>
            <div class="px-4">
            @forelse (array_filter($companies, fn ($c) => $c['status'] === 'Pending') as $c)
                <div class="d-flex align-items-center gap-3 py-3" style="{{ $loop->first ? '' : 'border-top:1px solid var(--emp-line);' }}">
                    <div class="co-logo" style="background: {{ $c['color'] }}" aria-hidden="true">{{ $c['initials'] }}</div>
                    <div class="flex-grow-1 min-w-0"><div class="fw-bold text-navy">{{ $c['name'] }}</div><div class="small text-muted-emp">{{ $c['industry'] }} · Registered {{ $c['registered'] }}</div></div>
                    <a href="{{ route('admin.companies', ['status' => 'Pending']) }}" class="btn btn-outline-navy btn-sm">Review</a>
                </div>
            @empty
                <div class="empty-state py-4"><i class="bi bi-patch-check"></i><p class="small text-muted-emp mb-0 mt-2">No companies are waiting for verification.</p></div>
            @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-emp mb-4">
            <div class="card-head"><h2 class="h6 mb-0">Who uses Employra</h2></div>
            <div class="p-4 small">
                <div class="d-flex justify-content-between mb-2"><span><i class="bi bi-people text-muted-emp me-2"></i>Users (candidates)</span><strong>{{ number_format($stats['users']) }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span><i class="bi bi-building text-muted-emp me-2"></i>Companies</span><strong>{{ number_format($stats['companies']) }}</strong></div>
                <div class="d-flex justify-content-between"><span><i class="bi bi-person-gear text-muted-emp me-2"></i>Admins</span><strong>{{ number_format($stats['admins']) }}</strong></div>
            </div>
        </div>
        <div class="card-emp mb-4">
            <div class="card-head"><h2 class="h6 mb-0">Admin activity</h2></div>
            <div class="p-4">
            <div class="num fs-2 fw-bold text-navy mb-1">{{ $activityCount }}</div>
            <div class="small text-muted-emp">actions recorded in the selected date range.</div>
            <a href="{{ route('admin.audit') }}" class="btn btn-outline-navy btn-sm w-100 mt-3">Open activity log</a>
            </div>
        </div>
        <div class="card-emp">
            <div class="card-head"><h2 class="h6 mb-0">Recent activity</h2></div>
            <div class="p-4">
            <ul class="activity">
                @forelse ($activity as $a)
                    <li><span class="ico {{ $a['tone'] }}"><i class="bi bi-{{ $a['icon'] }}"></i></span><div><div>{{ $a['text'] }}</div><div class="small text-muted-emp">{{ $a['time'] }}</div></div></li>
                @empty
                    <li><div class="small text-muted-emp">No administrator actions have been recorded yet.</div></li>
                @endforelse
            </ul>
            </div>
        </div>
    </div>
</div>
@endsection
