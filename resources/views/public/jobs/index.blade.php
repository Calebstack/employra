@extends('layouts.public')

@section('title', 'Find Jobs — Employra')

@section('content')
@php
    $salaryMin = max(0, min((int) old('salary_min', request('salary_min', 0)), 10000000));
    $salaryMax = max(0, min((int) old('salary_max', request('salary_max', 10000000)), 10000000));
    $salaryMinFilter = (int) request('salary_min', 0);
    $salaryMaxFilter = (int) request('salary_max', 10000000);
    $salaryFilterActive = $salaryMinFilter > 0 || $salaryMaxFilter < 10000000;
@endphp

<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Find Jobs</li>
            </ol>
        </nav>
        <h1 class="h2 mb-1">Find your next job</h1>
        <p class="mb-3">Browse open roles from verified employers across Nigeria.</p>

        <form action="{{ route('jobs.index') }}" method="GET" class="search-card" role="search">
            {{-- keep the sidebar filters when searching again --}}
            @foreach (['category', 'level', 'salary_min', 'salary_max'] as $keep)
                @if (request()->filled($keep))<input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">@endif
            @endforeach
            @foreach (['type', 'mode'] as $keepArr)
                @foreach ((array) request($keepArr, []) as $v)<input type="hidden" name="{{ $keepArr }}[]" value="{{ $v }}">@endforeach
            @endforeach

            <div class="row g-0 align-items-center">
                <div class="col-md-5">
                    <label for="q" class="visually-hidden">Job title or keyword</label>
                    <div class="input-icon">
                        <i class="bi bi-search"></i>
                        <input id="q" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Job title, skill or company">
                    </div>
                </div>
                <div class="col-md-4 divider">
                    <label for="state-search" class="visually-hidden">Nigerian state</label>
                    <div class="input-icon">
                        <i class="bi bi-geo-alt"></i>
                        <select id="state-search" name="state" class="form-select">
                            <option value="">Any Nigerian state</option>
                            @foreach ($states as $stateValue => $stateLabel)
                                <option value="{{ $stateValue }}" @selected(request('state') === $stateValue)>{{ $stateLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 p-1">
                    <button type="submit" class="btn btn-emp w-100">Search jobs</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="section-sm bg-soft">
    <div class="container">
        <div class="row g-4">

            {{-- ---------- Filters ---------- --}}
            <div class="col-lg-3">
                <button class="btn btn-outline-navy w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filters" aria-expanded="false" aria-controls="filters">
                    <i class="bi bi-funnel me-1"></i> Filters
                </button>

                <div class="collapse d-lg-block" id="filters">
                    <form action="{{ route('jobs.index') }}" method="GET" class="card-emp filter-card" data-autosubmit>
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="location" value="{{ request('location') }}">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 mb-0 text-navy"><i class="bi bi-sliders me-2 text-blue"></i>Filters</h2>
                            <a href="{{ route('jobs.index') }}" class="small fw-bold">Clear all</a>
                        </div>

                        <div class="pub-filter-group">
                            <h6>Nigerian state</h6>
                            <label for="f-state" class="visually-hidden">Nigerian state</label>
                            <select id="f-state" name="state" class="form-select">
                                <option value="">Any state</option>
                                @foreach ($states as $stateValue => $stateLabel)
                                    <option value="{{ $stateValue }}" @selected(request('state') === $stateValue)>{{ $stateLabel }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Remote jobs are included for every state.</div>
                        </div>

                        <div class="pub-filter-group">
                            <h6>Category</h6>
                            <label for="f-category" class="visually-hidden">Category</label>
                            <select id="f-category" name="category" class="form-select">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pub-filter-group">
                            <h6>Job type</h6>
                            @foreach ($types as $t)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="type[]" value="{{ $t }}" id="type-{{ $loop->index }}" @checked(in_array($t, (array) request('type', [])))>
                                    <label class="form-check-label" for="type-{{ $loop->index }}">{{ $t }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="pub-filter-group">
                            <h6>Work mode</h6>
                            @foreach ($modes as $m)
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="mode[]" value="{{ $m }}" id="mode-{{ $loop->index }}" @checked(in_array($m, (array) request('mode', [])))>
                                    <label class="form-check-label" for="mode-{{ $loop->index }}">{{ $m }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="pub-filter-group">
                            <h6>Monthly salary (NGN)</h6>
                            <div class="mb-3">
                                <label class="form-label d-flex justify-content-between" for="f-salary-min">
                                    <span>Minimum</span><strong data-salary-min-output></strong>
                                </label>
                                <input id="f-salary-min" name="salary_min" type="range" class="form-range" min="0" max="10000000" step="50000" value="{{ $salaryMin }}">
                                @error('salary_min')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label d-flex justify-content-between" for="f-salary-max">
                                    <span>Maximum</span><strong data-salary-max-output></strong>
                                </label>
                                <input id="f-salary-max" name="salary_max" type="range" class="form-range" min="0" max="10000000" step="50000" value="{{ $salaryMax }}">
                                @error('salary_max')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-text">Drag to adjust. Jobs with overlapping salary ranges are shown.</div>
                        </div>

                        <div class="pub-filter-group">
                            <h6>Experience level</h6>
                            <label for="f-level" class="visually-hidden">Experience level</label>
                            <select id="f-level" name="level" class="form-select">
                                <option value="">Any level</option>
                                @foreach ($levels as $lv)
                                    <option value="{{ $lv }}" @selected(request('level') === $lv)>{{ $lv }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filters apply automatically (see js/employra.js); this button is the no-JavaScript fallback --}}
                        <noscript><button type="submit" class="btn btn-navy w-100">Apply filters</button></noscript>
                    </form>
                </div>
            </div>

            {{-- ---------- Results ---------- --}}
            <div class="col-lg-9">
                <div class="job-results-scroll" role="region" tabindex="0" aria-label="Job listings">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <div class="fw-800 text-navy">{{ count($jobs) }} {{ count($jobs) === 1 ? 'job' : 'jobs' }} found</div>
                            @if (request('q') || request('location') || request('state') || $salaryFilterActive)
                                <div class="small text-muted-emp">
                                    Showing results
                                    @if (request('q')) for “{{ request('q') }}” @endif
                                    @if (request('location')) in “{{ request('location') }}” @endif
                                    @if (request('state')) in {{ $states[request('state')] ?? request('state') }} @endif
                                    @if ($salaryFilterActive)
                                        · Salary
                                        @if ($salaryMinFilter > 0)from ₦{{ number_format($salaryMinFilter) }}@endif
                                        @if ($salaryMinFilter > 0 && $salaryMaxFilter < 10000000)–@endif
                                        @if ($salaryMaxFilter < 10000000)up to ₦{{ number_format($salaryMaxFilter) }}@endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @forelse ($jobs as $job)
                        <div class="mb-3">
                            @include('components.job-card', ['job' => $job])
                        </div>
                    @empty
                        @include('components.empty-state', [
                            'icon' => 'search',
                            'title' => 'No jobs match your search',
                            'text' => 'Try fewer filters, check your spelling, or search for a broader skill like “developer” or “marketing”.',
                            'actionUrl' => route('jobs.index'),
                            'actionLabel' => 'Clear search and filters',
                        ])
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
