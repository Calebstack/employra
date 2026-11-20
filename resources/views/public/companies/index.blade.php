@extends('layouts.public')

@section('title', 'Companies — Employra')

@section('content')

<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Companies</li>
            </ol>
        </nav>
        <h1 class="h2 mb-1">Explore companies</h1>
        <p class="text-muted-emp mb-3">Learn about employers and see who is hiring right now.</p>

        <form action="{{ route('companies.index') }}" method="GET" class="search-card" role="search">
            <div class="row g-0 align-items-center">
                <div class="col-md-6">
                    <label for="company-q" class="visually-hidden">Company name or location</label>
                    <div class="input-icon">
                        <i class="bi bi-search"></i>
                        <input id="company-q" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Company name or location">
                    </div>
                </div>
                <div class="col-md-3 divider">
                    <label for="industry" class="visually-hidden">Industry</label>
                    <div class="input-icon">
                        <i class="bi bi-building"></i>
                        <select id="industry" name="industry" class="form-select">
                            <option value="">All industries</option>
                            @foreach ($industries as $ind)
                                <option value="{{ $ind }}" @selected(request('industry') === $ind)>{{ $ind }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 p-1">
                    <button type="submit" class="btn btn-emp w-100">Search</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="section-sm bg-soft">
    <div class="container">
        <div class="fw-800 text-navy mb-3">{{ count($companies) }} {{ count($companies) === 1 ? 'company' : 'companies' }}</div>

        <div class="row g-3">
            @forelse ($companies as $company)
                <div class="col-md-6 col-lg-4">
                    @include('components.company-card', ['company' => $company])
                </div>
            @empty
                <div class="col-12">
                    @include('components.empty-state', [
                        'icon' => 'building',
                        'title' => 'No companies found',
                        'text' => 'Try a different name or choose “All industries”.',
                        'actionUrl' => route('companies.index'),
                        'actionLabel' => 'Show all companies',
                    ])
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection
