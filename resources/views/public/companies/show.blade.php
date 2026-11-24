@extends('layouts.public')

@section('title', $company['name'].' — Employra')

@section('content')

<section class="bg-soft pb-2">
    <div class="container pt-4">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $company['name'] }}</li>
            </ol>
        </nav>

        <div class="card-emp overflow-hidden">
            <div class="cover"></div>
            <div class="px-4 pb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-start justify-content-between gap-3">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-start gap-3">
                        @if (!empty($company['logo_url']))
                            <img src="{{ $company['logo_url'] }}" alt="{{ $company['name'] }} logo" class="rounded object-fit-cover flex-none" width="82" height="82" style="margin-top: -42px;">
                        @else
                            <div class="co-logo lg flex-none" style="background: {{ $company['color'] }}; margin-top: -42px;" aria-hidden="true">{{ $company['initials'] }}</div>
                        @endif
                        <div class="pt-sm-3">
                            <h1 class="h3 mb-1">{{ $company['name'] }}</h1>
                            <div class="meta-row">
                                <span><i class="bi bi-building"></i>{{ $company['industry'] }}</span>
                                @if (!empty($company['location']))<span><i class="bi bi-geo-alt"></i>{{ $company['location'] }}</span>@endif
                                @if (!empty($company['size']))<span><i class="bi bi-people"></i>{{ $company['size'] }}</span>@endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 pt-md-3">
                        <a href="#open-roles" class="btn btn-emp">See open roles ({{ count($jobs) }})</a>
                        @if (!empty($company['website']))
                            <a href="{{ \Illuminate\Support\Str::startsWith($company['website'], ['http://', 'https://']) ? $company['website'] : 'https://'.$company['website'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-navy"><i class="bi bi-box-arrow-up-right me-1"></i>Website</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-sm bg-soft pt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="card-emp p-4 mb-4">
                    <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2" style="color:var(--emp-blue)"></i>About {{ $company['name'] }}</h2>
                    <p class="mb-0" style="line-height:1.75">{{ $company['about'] }}</p>
                </article>

                <div id="open-roles">
                    <h2 class="h5 mb-3"><i class="bi bi-briefcase me-2" style="color:var(--emp-blue)"></i>Open roles <span class="badge-soft ms-1">{{ count($jobs) }}</span></h2>

                    @forelse ($jobs as $job)
                        <div class="mb-3">
                            @include('components.job-card', ['job' => $job])
                        </div>
                    @empty
                        @include('components.empty-state', [
                            'icon' => 'briefcase',
                            'title' => 'No open roles right now',
                            'text' => 'This company has no active vacancies at the moment. Check back soon.',
                            'actionUrl' => route('jobs.index'),
                            'actionLabel' => 'Browse all jobs',
                        ])
                    @endforelse
                </div>
            </div>

            <aside class="col-lg-4">
                <div class="pub-side-sticky">
                <div class="card-emp p-4 mb-3">
                    <h2 class="h6 mb-2">Company details</h2>
                    <div class="summary-row"><span>Industry</span><span>{{ $company['industry'] }}</span></div>
                    @if (!empty($company['location']))<div class="summary-row"><span>Headquarters</span><span>{{ $company['location'] }}</span></div>@endif
                    @if (!empty($company['size']))<div class="summary-row"><span>Company size</span><span>{{ $company['size'] }}</span></div>@endif
                    @if (!empty($company['founded']))<div class="summary-row"><span>Founded</span><span>{{ $company['founded'] }}</span></div>@endif
                    @if (!empty($company['website']))<div class="summary-row"><span>Website</span><span>{{ $company['website'] }}</span></div>@endif
                </div>

                @if (auth()->user()?->role === 'candidate')
                    <details class="card-emp p-4 mb-3">
                        <summary class="fw-bold text-navy">Report this company</summary>
                        <form method="POST" action="{{ route('companies.report', $company['slug']) }}" class="mt-3">
                            @csrf
                            <label class="form-label" for="company-report-reason">Reason</label>
                            <select id="company-report-reason" name="reason" class="form-select mb-3 @error('reason') is-invalid @enderror" required>
                                <option value="">Choose a reason</option>
                                @foreach (['Fraudulent company information', 'Impersonation', 'Discrimination', 'Other'] as $reason)
                                    <option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ $reason }}</option>
                                @endforeach
                            </select>
                            @error('reason')<div class="invalid-feedback d-block mb-3">{{ $message }}</div>@enderror
                            <label class="form-label" for="company-report-details">Details</label>
                            <textarea id="company-report-details" name="details" class="form-control mb-3 @error('details') is-invalid @enderror" rows="3" minlength="10" maxlength="3000" required>{{ old('details') }}</textarea>
                            @error('details')<div class="invalid-feedback d-block mb-3">{{ $message }}</div>@enderror
                            <button type="submit" class="btn btn-outline-navy btn-sm">Submit report</button>
                        </form>
                    </details>
                @endif

                <div class="card-emp p-4" style="background: var(--emp-orange-100); border-color: #ffd9bd;">
                    <div class="fw-800 text-navy mb-1">Want to work here?</div>
                    <p class="small text-muted-emp mb-3">Create a free profile and apply with a CV you have already checked.</p>
                    <a href="{{ route('register', ['role' => 'candidate']) }}" class="btn btn-emp w-100">Create a free account</a>
                </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection
