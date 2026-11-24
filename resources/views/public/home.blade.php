@extends('layouts.public')

@section('title', 'Employra — Find · Hire · Grow')

@section('content')

{{-- ============ HERO ============ --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="eyebrow mb-3"><i class="bi bi-stars"></i> Clear hiring, people in charge</span>
                <h1 class="mb-3">Find the right job.<br><span class="accent">Hire the right people.</span></h1>
                <p class="lead mb-4">Employra helps candidates present their best selves and helps employers review applications fairly and quickly, with clear reasons behind every suggestion.</p>

                <form action="{{ route('jobs.index') }}" method="GET" class="search-card home-search-card mb-3" role="search">
                    <div class="input-icon search-field">
                        <label for="hero-q" class="visually-hidden">Job title or keyword</label>
                        <i class="bi bi-search"></i>
                        <input id="hero-q" type="text" name="q" class="form-control" placeholder="Job title or skill">
                    </div>
                    <div class="input-icon search-field search-field-location">
                        <label for="hero-state" class="visually-hidden">Nigerian state</label>
                        <i class="bi bi-geo-alt"></i>
                        <select id="hero-state" name="state" class="form-select">
                            <option value="">Any Nigerian state</option>
                            @foreach ($states as $stateValue => $stateLabel)
                                <option value="{{ $stateValue }}">{{ $stateLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-emp search-submit">Search</button>
                </form>

                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                    <span class="small text-muted-emp fw-semibold me-1">Popular:</span>
                    <a class="tag-link" href="{{ route('jobs.index', ['q' => 'PHP']) }}">PHP Developer</a>
                    <a class="tag-link" href="{{ route('jobs.index', ['q' => 'Data']) }}">Data Analyst</a>
                    <a class="tag-link" href="{{ route('jobs.index', ['mode' => ['Remote']]) }}">Remote</a>
                    <a class="tag-link" href="{{ route('jobs.index', ['type' => ['Internship']]) }}">Internships</a>
                </div>

                <div class="d-flex flex-wrap gap-4 small text-muted-emp fw-semibold">
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i>Free for candidates</span>
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i>Private by default</span>
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i>Trackable applications</span>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hero-visual" aria-hidden="true">
                    <div class="float-card fc-1">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-tile green"><i class="bi bi-shield-lock"></i></div>
                            <div>
                                <div class="fw-800 text-navy">Private CV storage</div>
                                <div class="small text-muted-emp">Choose the saved CV you want to share with each application.</div>
                            </div>
                        </div>
                    </div>

                    <div class="float-card fc-2">
                        @if (! empty($featuredJobs))
                            @php $featuredJob = $featuredJobs[0]; @endphp
                            <div class="d-flex align-items-center gap-3">
                                <div class="co-logo sm" style="background:{{ $featuredJob['company_color'] }}">{{ $featuredJob['company_initials'] }}</div>
                                <div class="flex-grow-1">
                                    <div class="fw-800 text-navy small">{{ $featuredJob['title'] }}</div>
                                    <div class="small text-muted-emp">{{ $featuredJob['company'] }} · {{ $featuredJob['location'] }}</div>
                                </div>
                            </div>
                            <a class="small fw-bold d-inline-block mt-3" href="{{ route('jobs.show', $featuredJob['slug']) }}">View live listing <i class="bi bi-arrow-right"></i></a>
                        @else
                            <div class="fw-800 text-navy">No open jobs yet</div>
                            <div class="small text-muted-emp">Verified-company listings will appear here.</div>
                        @endif
                    </div>

                    <div class="float-card fc-3">
                        <div class="small fw-800 text-navy mb-2">Application status</div>
                        <div class="d-flex align-items-center gap-2 small">
                            <span class="badge-soft green">Submitted</span><i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge-soft">Reviewing</span><i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge-soft grey">Interview</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ STATS ============ --}}
<section class="home-stats pb-2">
    <div class="container">
        <div class="stat-strip shadow-emp">
            <div class="row text-center g-3">
                @foreach ($stats as $stat)
                    <div class="col-6 col-md-3">
                        <div class="stat-num">{{ $stat['value'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============ TRUST BAND ============ --}}
<section class="pt-4 pb-0">
    <div class="container">
        <div class="row g-3 pub-trust">
            <div class="col-6 col-lg-3"><div class="pub-trust-item"><i class="bi bi-shield-check"></i><div><strong>Verified companies</strong><span>Employer profiles are reviewed</span></div></div></div>
            <div class="col-6 col-lg-3"><div class="pub-trust-item"><i class="bi bi-lock"></i><div><strong>Private CVs</strong><span>Shared only when you apply</span></div></div></div>
            <div class="col-6 col-lg-3"><div class="pub-trust-item"><i class="bi bi-chat-square-text"></i><div><strong>Clear reasons</strong><span>Explainable match feedback</span></div></div></div>
            <div class="col-6 col-lg-3"><div class="pub-trust-item"><i class="bi bi-people"></i><div><strong>People decide</strong><span>AI only suggests</span></div></div></div>
        </div>
    </div>
</section>

{{-- ============ HOW IT WORKS ============ --}}
<section class="section" id="how-it-works">
    <div class="container">
        <div class="text-center mb-4">
            <span class="eyebrow mb-3">How it works</span>
            <h2 class="section-title mb-2">Simple for both sides of hiring</h2>
            <p class="section-sub mx-auto">Whether you are looking for your next role or your next team member, Employra keeps every step clear.</p>
        </div>

        <div class="text-center mb-4">
            <ul class="nav nav-pills-emp" id="howTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-candidates" data-bs-toggle="pill" data-bs-target="#pane-candidates" type="button" role="tab" aria-controls="pane-candidates" aria-selected="true">I'm looking for a job</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-employers" data-bs-toggle="pill" data-bs-target="#pane-employers" type="button" role="tab" aria-controls="pane-employers" aria-selected="false">I'm hiring</button>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="pane-candidates" role="tabpanel" aria-labelledby="tab-candidates" tabindex="0">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">1</div><h3 class="h6">Create your profile</h3><p class="text-muted-emp small mb-0">Add your skills, education and experience once. You control what employers can see.</p></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">2</div><h3 class="h6">Upload your CV</h3><p class="text-muted-emp small mb-0">Upload a PDF or DOCX and get clear feedback on structure, wording and missing keywords.</p></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">3</div><h3 class="h6">Find &amp; apply</h3><p class="text-muted-emp small mb-0">Search jobs, save the ones you like and apply when you are signed in.</p></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">4</div><h3 class="h6">Track progress</h3><p class="text-muted-emp small mb-0">See every application move from submitted to interview and offer, all in one place.</p></div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('register', ['role' => 'candidate']) }}" class="btn btn-emp">Create a candidate account</a>
                </div>
            </div>

            <div class="tab-pane fade" id="pane-employers" role="tabpanel" aria-labelledby="tab-employers" tabindex="0">
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">1</div><h3 class="h6">Set up your company</h3><p class="text-muted-emp small mb-0">Create a company profile so candidates can learn who you are before they apply.</p></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">2</div><h3 class="h6">Post a vacancy</h3><p class="text-muted-emp small mb-0">Describe the role and mark must-have and nice-to-have skills. You can edit them any time.</p></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">3</div><h3 class="h6">Review with evidence</h3><p class="text-muted-emp small mb-0">Each application comes with a match summary that shows where requirements are met or missing.</p></div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="step-card pub-step"><div class="step-num mb-3">4</div><h3 class="h6">Decide &amp; schedule</h3><p class="text-muted-emp small mb-0">Move candidates through stages and book interviews. The final decision is always yours.</p></div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('register', ['role' => 'company']) }}" class="btn btn-navy">Create a company account</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ FEATURED JOBS ============ --}}
<section class="section bg-soft">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <span class="eyebrow mb-2">Fresh vacancies</span>
                <h2 class="section-title mb-0">Featured jobs</h2>
            </div>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-navy">Browse all jobs <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            @foreach ($featuredJobs as $job)
                <div class="col-lg-6">
                    @include('components.job-card', ['job' => $job])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CV CHECK ============ --}}
<section class="section" id="cv-check">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="eyebrow mb-3"><i class="bi bi-file-earmark-text"></i> CV library</span>
                <h2 class="section-title mb-3">Keep your CV ready for the next application</h2>
                <p class="text-muted-emp mb-4">Upload a CV once, keep it in your private account and choose which saved copy to attach when you apply.</p>

                <div class="d-flex gap-3 mb-3">
                    <div class="icon-tile"><i class="bi bi-lightning-charge"></i></div>
                    <div><div class="fw-800 text-navy">Upload once, reuse later</div><div class="text-muted-emp small">Choose a saved CV for each application without uploading it again.</div></div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="icon-tile orange"><i class="bi bi-pencil-square"></i></div>
                    <div><div class="fw-800 text-navy">You stay in control</div><div class="text-muted-emp small">Your CV is shared with a company only when you attach it to an application.</div></div>
                </div>
                <div class="d-flex gap-3 mb-4">
                    <div class="icon-tile green"><i class="bi bi-shield-check"></i></div>
                    <div><div class="fw-800 text-navy">Private by default</div><div class="text-muted-emp small">Only employers you apply to can see your CV.</div></div>
                </div>

                <a href="{{ route('register', ['role' => 'candidate']) }}" class="btn btn-emp">Create a candidate account</a>
            </div>

            <div class="col-lg-7">
                <div class="cv-showcase" aria-hidden="true">
                    <div class="cv-showcase-window">
                        <div class="cv-showcase-topbar">
                            <span class="cv-window-controls"><i></i><i></i><i></i></span>
                            <span class="cv-showcase-label">EMPLOYRA <span>/</span> CV LIBRARY</span>
                            <span class="cv-privacy"><i class="bi bi-lock-fill"></i> Private</span>
                        </div>
                        <div class="cv-showcase-body">
                            <div class="cv-profile-row">
                                <span class="cv-file-icon"><i class="bi bi-file-earmark-person"></i></span>
                                <div>
                                    <span class="cv-overline">YOUR CAREER TOOLKIT</span>
                                    <strong>Your CV library</strong>
                                    <span>Ready for the next opportunity</span>
                                </div>
                                <span class="cv-menu-icon"><i class="bi bi-three-dots"></i></span>
                            </div>
                            <div class="cv-document">
                                <span class="cv-document-type">PDF</span>
                                <div class="cv-document-copy">
                                    <strong>Resume</strong>
                                    <span>Saved in your private library</span>
                                </div>
                                <span class="cv-document-status"><i class="bi bi-check2-circle"></i> Saved</span>
                            </div>
                            <div class="cv-showcase-footer">
                                <span><i class="bi bi-shield-check"></i> You choose what to share</span>
                                <span><i class="bi bi-arrow-up-right"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="cv-float-card">
                        <span class="cv-float-icon"><i class="bi bi-person-check"></i></span>
                        <span><strong>You're in control</strong><small>Your CV is shared only when you apply.</small></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ ABOUT / WHY EMPLOYRA ============ --}}
<section class="section bg-soft" id="about">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <span class="eyebrow mb-3">About Employra</span>
                <h2 class="section-title mb-3">Hiring should be clear, fair and human</h2>
                <p class="section-sub mx-auto">Employra makes the path between candidates and employers clearer by keeping job details, applications and hiring updates in one place.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="pillar"><div class="icon-tile mb-3"><i class="bi bi-shield-lock"></i></div><h3 class="h6">Privacy first</h3><p class="text-muted-emp small mb-0">CVs and contact details are private by default. You decide who sees them.</p></div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pillar"><div class="icon-tile orange mb-3"><i class="bi bi-search-heart"></i></div><h3 class="h6">Clear information</h3><p class="text-muted-emp small mb-0">Job listings and application updates stay available to the people involved.</p></div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pillar"><div class="icon-tile green mb-3"><i class="bi bi-people"></i></div><h3 class="h6">People decide</h3><p class="text-muted-emp small mb-0">Companies review candidates and make every hiring decision themselves.</p></div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="pillar"><div class="icon-tile mb-3"><i class="bi bi-person-check"></i></div><h3 class="h6">Fair by design</h3><p class="text-muted-emp small mb-0">We do not infer age, gender, ethnicity, religion or other protected traits.</p></div>
            </div>
        </div>
    </div>
</section>

{{-- ============ FEATURED COMPANIES ============ --}}
<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <span class="eyebrow mb-2">Who is hiring</span>
                <h2 class="section-title mb-0">Companies on Employra</h2>
            </div>
            <a href="{{ route('companies.index') }}" class="btn btn-outline-navy">View all companies <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            @foreach ($featuredCompanies as $company)
                <div class="col-md-6 col-lg-3">
                    @include('components.company-card', ['company' => $company])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="section pt-0">
    <div class="container">
        <div class="cta-band">
            <div class="row align-items-center g-4 cta-content">
                <div class="col-lg-7">
                    <h2 class="mb-2">Ready to find, hire and grow?</h2>
                    <p class="mb-0">Join as a candidate to store your CV and track applications, or as a company to post jobs and manage applicants.</p>
                </div>
                <div class="col-lg-5 d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('register', ['role' => 'candidate']) }}" class="btn btn-emp">I'm a candidate</a>
                    <a href="{{ route('register', ['role' => 'company']) }}" class="btn btn-light text-navy">I'm a company</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
