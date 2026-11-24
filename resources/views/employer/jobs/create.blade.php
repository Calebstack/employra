@extends('layouts.employer')
@section('title', 'Post a job')

@section('content')
<div class="page-banner">
    <nav aria-label="breadcrumb"><ol class="breadcrumb small mb-2"><li class="breadcrumb-item"><a href="{{ route('employer.jobs') }}" class="text-white-50">My jobs</a></li><li class="breadcrumb-item active text-white" aria-current="page">Post a job</li></ol></nav>
    <h1 class="page-title">Post a job</h1>
    <p>Clear requirements help us match the right candidates and explain every match.</p>
</div>

<form action="{{ route('employer.jobs.store') }}" method="POST" class="needs-validation" novalidate>
    @csrf
    @if ($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <div class="fw-bold">Please correct the highlighted job details.</div>
            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-emp p-3 p-md-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="icon-tile"><i class="bi bi-briefcase"></i></div><div><h2 class="h5 mb-0">1. Job basics</h2></div></div>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label" for="title">Job title</label><input id="title" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. PHP / Laravel Developer" required><div class="invalid-feedback">Please enter a job title.</div></div>
                    <div class="col-md-6"><label class="form-label" for="category">Category</label><select id="category" name="category" class="form-select">@foreach ($categories as $c)<option value="{{ $c }}" @selected(old('category') === $c)>{{ $c }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label" for="experience-level">Experience level</label><select id="experience-level" name="experience_level" class="form-select">@foreach ($levels as $l)<option value="{{ $l }}" @selected(old('experience_level') === $l)>{{ $l }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label" for="job-type">Job type</label><select id="job-type" name="job_type" class="form-select">@foreach ($types as $t)<option value="{{ $t }}" @selected(old('job_type') === $t)>{{ $t }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label" for="work-mode">Work mode</label><select id="work-mode" name="work_mode" class="form-select">@foreach ($modes as $m)<option value="{{ $m }}" @selected(old('work_mode') === $m)>{{ $m }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label" for="location">Location</label><input id="location" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror" placeholder="e.g. Lagos, Nigeria" required></div>
                    <div class="col-md-6">
                        <label class="form-label">Monthly salary range (NGN) <span class="text-muted-emp fw-normal">(optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span><input name="salary_min" value="{{ old('salary_min') }}" type="number" min="0" max="10000000" class="form-control" placeholder="Minimum" aria-label="Minimum monthly salary">
                            <span class="input-group-text">to</span><input name="salary_max" value="{{ old('salary_max') }}" type="number" min="0" max="10000000" class="form-control" placeholder="Maximum" aria-label="Maximum monthly salary">
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label" for="deadline">Application deadline</label><input id="deadline" name="deadline" value="{{ old('deadline') }}" type="date" class="form-control" min="{{ now()->toDateString() }}"></div>
                </div>
            </div>

            <div class="card-emp p-3 p-md-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="icon-tile"><i class="bi bi-card-text"></i></div><div><h2 class="h5 mb-0">2. About the role</h2></div></div>
                <div class="mb-3"><label class="form-label" for="summary">Summary</label><textarea id="summary" name="summary" class="form-control" rows="3" placeholder="Two or three sentences about the role." required>{{ old('summary') }}</textarea></div>
                <div class="mb-3"><label class="form-label" for="responsibilities">Responsibilities</label><textarea id="responsibilities" name="responsibilities" class="form-control" rows="4" placeholder="One responsibility per line.">{{ old('responsibilities') }}</textarea></div>
                <div class="mb-0"><label class="form-label" for="benefits">Benefits</label><textarea id="benefits" name="benefits" class="form-control" rows="3" placeholder="One benefit per line.">{{ old('benefits') }}</textarea></div>
            </div>

            <div class="card-emp p-3 p-md-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3"><div class="icon-tile"><i class="bi bi-list-check"></i></div><div><h2 class="h5 mb-0">3. Requirements</h2><p class="small text-muted-emp mb-0">These drive the match. Keep to what is truly needed.</p></div></div><div class="mb-3"></div>
                <div class="mb-3"><label class="form-label">Must-have skills</label>
                    <div class="tag-input" data-tag-input data-name="required_skills[]">
                        @foreach ((array) old('required_skills', []) as $skill)<span class="skill-chip">{{ $skill }}<input type="hidden" name="required_skills[]" value="{{ $skill }}"><button type="button" aria-label="Remove {{ $skill }}">&times;</button></span>@endforeach
                        <input type="text" placeholder="Type a skill and press Enter" aria-label="Add must-have skill">
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Nice-to-have skills</label>
                    <div class="tag-input" data-tag-input data-name="preferred_skills[]">
                        @foreach ((array) old('preferred_skills', []) as $skill)<span class="skill-chip">{{ $skill }}<input type="hidden" name="preferred_skills[]" value="{{ $skill }}"><button type="button" aria-label="Remove {{ $skill }}">&times;</button></span>@endforeach
                        <input type="text" placeholder="Type a skill and press Enter" aria-label="Add nice-to-have skill">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="min-experience">Minimum years of experience</label><input id="min-experience" name="min_experience_years" type="number" min="0" max="60" class="form-control" value="{{ old('min_experience_years', 0) }}"></div>
                    <div class="col-md-6"><label class="form-label" for="minimum-education">Minimum education</label><select id="minimum-education" name="minimum_education" class="form-select"><option value="">No requirement</option>@foreach (['OND / NCE', 'HND / B.Sc.', 'M.Sc.'] as $education)<option value="{{ $education }}" @selected(old('minimum_education') === $education)>{{ $education }}</option>@endforeach</select></div>
                </div>
                <div class="alert alert-info small mt-4 mb-0"><i class="bi bi-shield-check me-1"></i>Employra never scores candidates on age, gender, name, photo, tribe, religion or marital status. Please do not list them as requirements.</div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-emp p-4 position-sticky" style="top: 90px;">
                <h2 class="h6 mb-3"><i class="bi bi-send me-1 text-orange"></i>Publish</h2>
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="notify_applicants" value="1" id="notify" @checked(old('notify_applicants', true))><label class="form-check-label" for="notify">Email me about new applicants</label></div>
                <button class="btn btn-emp w-100 mb-2" name="status" value="open">Publish job</button>
                <button class="btn btn-outline-navy w-100 mb-3" name="status" value="draft">Save as draft</button>
                <div class="small text-muted-emp">Your job appears on the public job board once published. You can close it any time.</div>
            </div>
        </div>
    </div>
</form>
@endsection
