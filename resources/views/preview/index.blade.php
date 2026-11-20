@extends('layouts.public')

@section('title', 'GUI Preview — Employra')

@section('content')
<section class="page-head">
    <div class="container">
        <span class="eyebrow mb-2"><i class="bi bi-eye"></i> Development preview</span>
        <h1 class="h2 mb-1">Employra screens</h1>
        <p class="text-muted-emp mb-0">Every finished screen is listed here. This page and <code>routes/gui-preview.php</code> are temporary and will be removed once the backend is connected.</p>
    </div>
</section>

<section class="section-sm bg-soft">
    <div class="container">

        <h2 class="h5 mb-3">Batch 1 · Public pages <span class="badge-soft green ms-1">Ready</span></h2>
        <div class="row g-3 preview-grid mb-5">
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('home') }}"><div class="fw-800 text-navy">Landing page</div><div class="path">/</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('jobs.index') }}"><div class="fw-800 text-navy">Job search &amp; filters</div><div class="path">/jobs</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('jobs.show', $sampleJob['slug']) }}"><div class="fw-800 text-navy">Job detail (with “Sign in to apply”)</div><div class="path">/jobs/{slug}</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('jobs.index', ['q' => 'zzzz']) }}"><div class="fw-800 text-navy">Job search · empty state</div><div class="path">/jobs?q=zzzz</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('companies.index') }}"><div class="fw-800 text-navy">Company directory</div><div class="path">/companies</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('companies.show', $sampleCompany['slug']) }}"><div class="fw-800 text-navy">Company profile</div><div class="path">/companies/{slug}</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('login') }}"><div class="fw-800 text-navy">Sign in</div><div class="path">/login</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('register', ['role' => 'candidate']) }}"><div class="fw-800 text-navy">Register · candidate</div><div class="path">/register?role=candidate</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('register', ['role' => 'company']) }}"><div class="fw-800 text-navy">Register · company</div><div class="path">/register?role=company</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('password.request') }}"><div class="fw-800 text-navy">Forgot password</div><div class="path">/forgot-password</div></a></div>
        </div>

        <h2 class="h5 mb-3">Batch 2 · Candidate area <span class="badge-soft green ms-1">Ready</span></h2>
        <div class="row g-3 preview-grid mb-5">
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.dashboard') }}"><div class="fw-800 text-navy">Dashboard</div><div class="path">/candidate</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.recommended') }}"><div class="fw-800 text-navy">Recommended jobs (with reasons)</div><div class="path">/candidate/recommended</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.saved') }}"><div class="fw-800 text-navy">Saved jobs</div><div class="path">/candidate/saved</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.applications') }}"><div class="fw-800 text-navy">My applications</div><div class="path">/candidate/applications</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.applications.show', 101) }}"><div class="fw-800 text-navy">Application detail · interview</div><div class="path">/candidate/applications/101</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.applications.show', 105) }}"><div class="fw-800 text-navy">Application detail · closed</div><div class="path">/candidate/applications/105</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.cvs') }}"><div class="fw-800 text-navy">CV library &amp; upload</div><div class="path">/candidate/cvs</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.cvs.show', 1) }}"><div class="fw-800 text-navy">CV analysis</div><div class="path">/candidate/cvs/1</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.profile') }}"><div class="fw-800 text-navy">Profile</div><div class="path">/candidate/profile</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('candidate.settings') }}"><div class="fw-800 text-navy">Settings</div><div class="path">/candidate/settings</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('jobs.apply', $sampleJob['slug']) }}"><div class="fw-800 text-navy">Apply form</div><div class="path">/jobs/{slug}/apply</div></a></div>
        </div>

        <h2 class="h5 mb-3">Batch 3 · Employer area <span class="badge-soft green ms-1">Ready</span></h2>
        <div class="row g-3 preview-grid mb-5">
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.dashboard') }}"><div class="fw-800 text-navy">Dashboard</div><div class="path">/employer</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.jobs') }}"><div class="fw-800 text-navy">My jobs</div><div class="path">/employer/jobs</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.jobs.create') }}"><div class="fw-800 text-navy">Post a job</div><div class="path">/employer/jobs/create</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.jobs.show', 1) }}"><div class="fw-800 text-navy">Ranked applicants (explainable)</div><div class="path">/employer/jobs/1</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.jobs.show', 1, ['stage' => 'New']) }}"><div class="fw-800 text-navy">Applicants · New only</div><div class="path">/employer/jobs/1?stage=New</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.applicants.show', 11) }}"><div class="fw-800 text-navy">Applicant review &amp; evidence</div><div class="path">/employer/applicants/11</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.applicants.show', 16) }}"><div class="fw-800 text-navy">Applicant · declined</div><div class="path">/employer/applicants/16</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.interviews') }}"><div class="fw-800 text-navy">Interviews</div><div class="path">/employer/interviews</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('employer.company') }}"><div class="fw-800 text-navy">Company profile</div><div class="path">/employer/company</div></a></div>
        </div>

        <h2 class="h5 mb-3">Batch 4 · Admin area <span class="badge-soft green ms-1">Ready</span></h2>
        <div class="row g-3 preview-grid mb-5">
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.dashboard') }}"><div class="fw-800 text-navy">Dashboard</div><div class="path">/admin</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.users') }}"><div class="fw-800 text-navy">Users</div><div class="path">/admin/users</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.companies', ['status' => 'Pending']) }}"><div class="fw-800 text-navy">Companies · verification queue</div><div class="path">/admin/companies</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.jobs') }}"><div class="fw-800 text-navy">Jobs moderation</div><div class="path">/admin/jobs</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.reports') }}"><div class="fw-800 text-navy">Reports</div><div class="path">/admin/reports</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.audit') }}"><div class="fw-800 text-navy">AI &amp; fairness log</div><div class="path">/admin/ai-log</div></a></div>
            <div class="col-md-6 col-lg-4"><a class="card-emp card-hover" href="{{ route('admin.settings') }}"><div class="fw-800 text-navy">Platform settings</div><div class="path">/admin/settings</div></a></div>
        </div>
    </div>
</section>
@endsection
