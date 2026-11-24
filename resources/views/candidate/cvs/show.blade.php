@extends('layouts.app')
@section('title', 'CV analysis')

@section('content')
<div class="page-banner cv-analysis-banner">
    <nav aria-label="breadcrumb"><ol class="breadcrumb small mb-2">
        <li class="breadcrumb-item"><a href="{{ route('candidate.cvs') }}" class="text-white-50">My CVs</a></li>
        <li class="breadcrumb-item active text-white">Analysis</li>
    </ol></nav>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="min-w-0">
            <h1 class="page-title">CV analysis</h1>
            <p class="text-break"><i class="bi bi-file-earmark-pdf me-1"></i>{{ $cv['filename'] }} · analyzed {{ $analysis['analyzed_at'] }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="#" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Re-analyze</a>
            <a href="{{ route('candidate.cvs') }}#upload" class="btn btn-emp btn-sm"><i class="bi bi-upload me-1"></i>Upload improved CV</a>
        </div>
    </div>
</div>

<div class="alert alert-info cv-analysis-note" role="note"><i class="bi bi-info-circle me-2" aria-hidden="true"></i><span>This report is guidance, not a verdict on you. Every point explains why it was suggested, and you decide what to change.</span></div>

<div class="row g-4">
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card-emp p-3 position-sticky section-nav" style="top: 90px;">
            <a href="#overview">Overview</a><a href="#extracted">What we found</a><a href="#strengths">Strengths</a><a href="#improve">Improvements</a><a href="#compare">Compare with a job</a>
        </div>
    </div>

    <div class="col-lg-9">
        <section id="overview" class="card-emp p-4 mb-4 analysis-overview">
            <div class="d-flex flex-wrap gap-4 align-items-center mb-3">
                <div class="text-center">
                    <div class="score-ring lg" style="--p: {{ $analysis['readiness'] }}"><span>{{ $analysis['readiness'] }}</span></div>
                    <div class="small fw-bold mt-2 text-navy">Readiness score</div>
                </div>
                <div class="flex-grow-1" style="min-width: 240px;">
                    <h2 class="h5">Summary</h2>
                    <p class="mb-0">{{ $analysis['summary'] }}</p>
                </div>
            </div>
            <div class="small text-muted-emp mb-3">The score reflects clarity, structure, achievements and keywords. It does not measure your worth or potential.</div>
            @foreach ($analysis['ratings'] as $r)
                <div class="rating-row">
                    <div class="fw-bold text-navy" style="width: 120px;">{{ $r['label'] }}</div>
                    <span class="badge-soft {{ $r['tone'] }}">{{ $r['level'] }}</span>
                    <div class="small text-muted-emp">{{ $r['note'] }}</div>
                </div>
            @endforeach
        </section>

        <section id="extracted" class="card-emp p-4 mb-4 analysis-panel">
            <h2 class="h5"><i class="bi bi-search text-orange me-2"></i>What we found in your CV</h2>
            <p class="small text-muted-emp">Check this is right. If something is wrong, fix your CV or edit your profile.</p>
            <div class="fw-bold small text-navy mb-2">Skills</div>
            <div class="mb-3">@foreach ($analysis['extracted']['skills'] as $s)<span class="skill-chip">{{ $s }}</span> @endforeach</div>
            <div class="fw-bold small text-navy mb-2">Experience</div>
            <ul class="small mb-3">@foreach ($analysis['extracted']['experience'] as $e)<li>{{ $e }}</li>@endforeach</ul>
            <div class="fw-bold small text-navy mb-2">Education</div>
            <ul class="small mb-0">@foreach ($analysis['extracted']['education'] as $e)<li>{{ $e }}</li>@endforeach</ul>
        </section>

        <section id="strengths" class="card-emp p-4 mb-4 analysis-panel">
            <h2 class="h5 mb-3"><i class="bi bi-hand-thumbs-up text-orange me-2"></i>Strengths</h2>
            @foreach ($analysis['strengths'] as $s)
                <div class="d-flex gap-3 mb-3">
                    <div class="icon-tile green"><i class="bi bi-check-lg"></i></div>
                    <div><div class="fw-bold text-navy">{{ $s['title'] }} <span class="small text-muted-emp fw-normal">· {{ $s['section'] }}</span></div><div class="small text-muted-emp">{{ $s['detail'] }}</div></div>
                </div>
            @endforeach
        </section>

        <section id="improve" class="card-emp p-4 mb-4 analysis-panel">
            <h2 class="h5 mb-3"><i class="bi bi-wrench-adjustable text-orange me-2"></i>What to improve</h2>
            @foreach ($analysis['improvements'] as $i)
                <div class="entry">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="priority {{ strtolower($i['priority']) }}">{{ $i['priority'] }}</span>
                        <span class="fw-bold text-navy">{{ $i['title'] }}</span>
                        <span class="small text-muted-emp">· {{ $i['section'] }}</span>
                    </div>
                    <div class="small">{{ $i['detail'] }}</div>
                    @if ($i['example'])
                        <div class="example-box"><strong>Example:</strong> {{ $i['example'] }}</div>
                    @endif
                </div>
            @endforeach
        </section>

        <section id="compare" class="card-emp p-4 analysis-panel">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1"><i class="bi bi-intersect text-orange me-2"></i>Compare with a job</h2>
                    <p class="small text-muted-emp mb-0">See how your CV lines up with a job before you apply.</p>
                </div>
                <select class="form-select" style="max-width: 320px;" aria-label="Choose a job to compare">
                    <option>{{ $analysis['compare']['job_title'] }} · {{ $analysis['compare']['company'] }}</option>
                    <option>Frontend Intern · BrightPath Technologies</option>
                    <option>Junior Data Analyst · NovaPay</option>
                </select>
            </div>

            <div class="fw-bold text-navy mb-2"><i class="bi bi-check-circle-fill text-success me-1"></i>Matches ({{ count($analysis['compare']['matched']) }})</div>
            @foreach ($analysis['compare']['matched'] as $m)
                <div class="evidence mb-2"><strong>{{ $m['skill'] }}</strong> <span class="small text-muted-emp">— {{ $m['evidence'] }} ({{ $m['section'] }})</span></div>
            @endforeach

            <div class="fw-bold text-navy mt-4 mb-2"><i class="bi bi-dash-circle-fill text-orange me-1"></i>Partly there ({{ count($analysis['compare']['partial']) }})</div>
            @foreach ($analysis['compare']['partial'] as $m)
                <div class="evidence mb-2"><strong>{{ $m['skill'] }}</strong> <span class="small text-muted-emp">— {{ $m['evidence'] }} ({{ $m['section'] }})</span></div>
            @endforeach

            <div class="fw-bold text-navy mt-4 mb-2"><i class="bi bi-plus-circle-fill text-muted me-1"></i>Not shown on your CV</div>
            <div class="mb-3">@foreach ($analysis['compare']['missing'] as $m)<span class="skill-chip" style="background:#eef1f6;color:#475467;">{{ $m }}</span> @endforeach</div>
            <div class="small text-muted-emp mb-3">“Not shown” does not mean you lack the skill. If you have it, add it to your CV with an example.</div>
            <a href="{{ route('jobs.apply', $analysis['compare']['job_slug']) }}" class="btn btn-emp">Apply to this job</a>
        </section>
    </div>
</div>
@endsection
