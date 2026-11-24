@extends('layouts.public')

@section('title', $job['title'].' at '.$job['company'].' — Employra')

@section('content')

<section class="page-head">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">Find Jobs</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $job['title'] }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row gap-4 align-items-md-center justify-content-between">
            <div class="d-flex gap-3 align-items-center min-w-0">
                @if (!empty($job['company_logo_url']))
                    <img src="{{ $job['company_logo_url'] }}" alt="{{ $job['company'] }} logo" width="72" height="72" class="rounded object-fit-cover flex-none">
                @else
                    <div class="co-logo lg flex-none" style="background: {{ $job['company_color'] }}" aria-hidden="true">{{ $job['company_initials'] }}</div>
                @endif
                <div class="min-w-0">
                    <h1 class="h2 mb-1">{{ $job['title'] }}</h1>
                    <a href="{{ route('companies.show', $job['company_slug']) }}" class="fw-bold text-white text-decoration-underline">{{ $job['company'] }}</a>
                    <div class="meta-row mt-2">
                        <span><i class="bi bi-geo-alt"></i>{{ $job['location'] }}</span>
                        <span><i class="bi bi-briefcase"></i>{{ $job['type'] }}</span>
                        <span><i class="bi bi-laptop"></i>{{ $job['mode'] }}</span>
                        <span><i class="bi bi-clock"></i>Posted {{ $job['posted'] }}</span>
                    </div>
                    <div class="mt-2"><span class="badge-soft"><i class="bi bi-cash-coin"></i>{{ $job['salary'] }}</span></div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center flex-none">
                <button type="button" class="save-btn" style="width:36px;height:36px" data-save="{{ $job['slug'] }}" aria-label="Save job" aria-pressed="false"><i class="bi bi-bookmark"></i></button>
                @guest
                    <button type="button" class="btn btn-emp" data-bs-toggle="modal" data-bs-target="#applyModal">Apply now</button>
                @endguest
                @auth
                    <a href="#apply" class="btn btn-emp">Apply now</a>
                @endauth
            </div>
        </div>
    </div>
</section>

<section class="section-sm bg-soft">
    <div class="container">
        <div class="row g-4">

            {{-- ---------- Main column ---------- --}}
            <div class="col-lg-8">
                <article class="card-emp p-4 mb-4 pub-section-card">
                    <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2 text-blue" style="color:var(--emp-blue)"></i>About the role</h2>
                    <p class="mb-0" style="line-height:1.75">{{ $job['summary'] }}</p>
                </article>

                <article class="card-emp p-4 mb-4 pub-section-card">
                    <h2 class="h5 mb-3"><i class="bi bi-list-check me-2 text-blue" style="color:var(--emp-blue)"></i>Responsibilities</h2>
                    <ul class="detail-list nice list-unstyled mb-0">
                        @foreach ($job['responsibilities'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="card-emp p-4 mb-4 pub-section-card">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h2 class="h5 mb-3"><i class="bi bi-check2-circle me-2 text-blue" style="color:var(--emp-blue)"></i>Must-have</h2>
                            <ul class="detail-list list-unstyled mb-0">
                                @foreach ($job['must'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h2 class="h5 mb-3"><i class="bi bi-plus-circle me-2 text-blue" style="color:var(--emp-blue)"></i>Nice-to-have</h2>
                            <ul class="detail-list nice list-unstyled mb-0">
                                @foreach ($job['nice'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </article>

                <article class="card-emp p-4 mb-4 pub-section-card">
                    <h2 class="h5 mb-3"><i class="bi bi-stars me-2 text-blue" style="color:var(--emp-blue)"></i>Skills</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($job['skills'] as $skill)
                            <span class="skill-chip">{{ $skill }}</span>
                        @endforeach
                    </div>
                </article>

                <article class="card-emp p-4 mb-4 pub-section-card">
                    <h2 class="h5 mb-3"><i class="bi bi-gift me-2 text-blue" style="color:var(--emp-blue)"></i>Benefits</h2>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($job['benefits'] as $b)
                            <span class="badge-soft green"><i class="bi bi-check2 me-1"></i>{{ $b }}</span>
                        @endforeach
                    </div>
                </article>

                <div class="card-emp p-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 pub-apply-card" id="apply">
                    <div>
                        <h2 class="h5 mb-1">Interested in this role?</h2>
                        <p class="text-muted-emp mb-0">Apply before {{ $job['deadline'] }}. You will be asked to sign in so we can attach your profile and CV.</p>
                    </div>
                    @guest
                        <button type="button" class="btn btn-emp flex-none" data-bs-toggle="modal" data-bs-target="#applyModal">Apply now</button>
                    @endguest
                    @auth
                        <a href="{{ route('jobs.apply', $job['slug']) }}" class="btn btn-emp flex-none">Apply now</a>
                    @endauth
                </div>
            </div>

            {{-- ---------- Sidebar ---------- --}}
            <aside class="col-lg-4">
                <div class="pub-side-sticky">
                <div class="card-emp p-4 mb-3 pub-apply-card">
                    <div class="eyebrow mb-1">Salary</div>
                    <div class="salary h4 mb-3">{{ $job['salary'] }}</div>
                    @guest
                        <button type="button" class="btn btn-emp btn-lg w-100 mb-3" data-bs-toggle="modal" data-bs-target="#applyModal">Apply now</button>
                    @endguest
                    @auth
                        <a href="{{ route('jobs.apply', $job['slug']) }}" class="btn btn-emp btn-lg w-100 mb-3">Apply now</a>
                    @endauth
                    <h2 class="h6 mb-2">Job summary</h2>
                    <div class="summary-row"><span>Job type</span><span>{{ $job['type'] }}</span></div>
                    <div class="summary-row"><span>Work mode</span><span>{{ $job['mode'] }}</span></div>
                    <div class="summary-row"><span>Experience</span><span>{{ $job['level'] }}</span></div>
                    <div class="summary-row"><span>Category</span><span>{{ $job['category'] }}</span></div>
                    <div class="summary-row"><span>Apply by</span><span>{{ $job['deadline'] }}</span></div>
                </div>

                <div class="card-emp p-4 mb-3">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if (!empty($company['logo_url']))
                            <img src="{{ $company['logo_url'] }}" alt="{{ $company['name'] }} logo" width="48" height="48" class="rounded object-fit-cover">
                        @else
                            <div class="co-logo" style="background: {{ $job['company_color'] }}" aria-hidden="true">{{ $job['company_initials'] }}</div>
                        @endif
                        <div>
                            <div class="fw-800 text-navy">{{ $company['name'] }}</div>
                            <div class="small text-muted-emp">{{ $company['industry'] }}</div>
                        </div>
                    </div>
                    <p class="small text-muted-emp">{{ $company['short'] }}</p>
                    <a href="{{ route('companies.show', $company['slug']) }}" class="btn btn-outline-navy w-100">View company profile</a>
                </div>

                <div class="card-emp p-4" style="background: var(--emp-blue-100); border-color: #cddcff;">
                    <div class="d-flex gap-2">
                        <i class="bi bi-file-earmark-text fs-4 text-blue" style="color: var(--emp-blue)"></i>
                        <div>
                            <div class="fw-800 text-navy">Is your CV ready?</div>
                            <p class="small text-muted-emp mb-2">Get feedback on your CV for this role before you apply.</p>
                            <a href="{{ route('register', ['role' => 'candidate']) }}" class="fw-bold small">Try CV Check <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                @if (($job['is_persisted'] ?? false) && auth()->user()?->role === 'candidate')
                    <details class="card-emp p-4 mt-3">
                        <summary class="fw-bold text-navy">Report this job</summary>
                        <form method="POST" action="{{ route('jobs.report', $job['slug']) }}" class="mt-3">
                            @csrf
                            <label class="form-label" for="report-reason">Reason</label>
                            <select id="report-reason" name="reason" class="form-select mb-3" required>
                                <option value="">Choose a reason</option>
                                @foreach (['Asks candidates for payment', 'Misleading information', 'Discrimination', 'Other'] as $reason)
                                    <option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ $reason }}</option>
                                @endforeach
                            </select>
                            <label class="form-label" for="report-details">Details</label>
                            <textarea id="report-details" name="details" class="form-control mb-3" rows="3" minlength="10" maxlength="3000" required>{{ old('details') }}</textarea>
                            <button type="submit" class="btn btn-outline-navy btn-sm">Submit report</button>
                        </form>
                    </details>
                @endif
                </div>
            </aside>
        </div>

        {{-- ---------- Similar jobs ---------- --}}
        <div class="mt-5">
            <h2 class="h4 mb-3">Similar jobs</h2>
            <div class="row g-4">
                @foreach ($similar as $s)
                    <div class="col-md-6 col-lg-4">
                        @include('components.job-card', ['job' => $s])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ---------- "Sign in to apply" modal (guests) ---------- --}}
@guest
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 18px;">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title h5" id="applyModalLabel">Sign in to apply</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted-emp">Create a free candidate account or sign in to apply for <strong class="text-navy">{{ $job['title'] }}</strong> at {{ $job['company'] }}. You can add your profile and CV in a few minutes.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn btn-emp">Sign in</a>
                    <a href="{{ route('register', ['role' => 'candidate', 'redirect' => url()->current()]) }}" class="btn btn-outline-navy">Create a free account</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endguest

@endsection
