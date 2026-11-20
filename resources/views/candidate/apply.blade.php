@extends('layouts.app')
@section('title', 'Apply')

@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb small">
    <li class="breadcrumb-item"><a href="{{ route('jobs.show', $job['slug']) }}">{{ $job['title'] }}</a></li>
    <li class="breadcrumb-item active">Apply</li>
</ol></nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-emp p-4 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="co-logo" style="background: {{ $job['company_color'] }}" aria-hidden="true">{{ $job['company_initials'] }}</div>
                <div>
                    <h1 class="h4 mb-1">Apply for {{ $job['title'] }}</h1>
                    <div class="text-muted-emp">{{ $job['company'] }} · {{ $job['location'] }} · {{ $job['mode'] }}</div>
                </div>
            </div>
        </div>

        <form action="{{ route('jobs.apply.submit', $job['slug']) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">1. Your details</h2>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Full name</label><input class="form-control" value="{{ $candidate['name'] }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="{{ $candidate['email'] }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input class="form-control" value="{{ $candidate['phone'] }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Location</label><input class="form-control" value="{{ $candidate['location'] }}" readonly></div>
                </div>
                <div class="small text-muted-emp mt-2">These come from your profile. <a href="{{ route('candidate.profile') }}">Edit profile</a></div>
            </div>

            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">2. Choose your CV</h2>
                @foreach ($cvs as $cv)
                    @if ($cv['status'] !== 'Failed')
                        <div class="form-check border rounded-3 p-3 ps-5 mb-2">
                            <input class="form-check-input" type="radio" name="cv_id" id="cv{{ $cv['id'] }}" value="{{ $cv['id'] }}" {{ $cv['primary'] ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="cv{{ $cv['id'] }}"><span class="fw-bold text-navy">{{ $cv['filename'] }}</span><span class="small text-muted-emp d-block">Uploaded {{ $cv['uploaded'] }} · {{ $cv['size'] }}</span></label>
                        </div>
                    @endif
                @endforeach
                @if (count(array_filter($cvs, fn ($cv) => $cv['status'] !== 'Failed')) === 0)
                    <p class="small text-muted-emp">You do not have a saved CV yet. Upload one to reuse it when applying.</p>
                @endif
                <a href="{{ route('candidate.cvs') }}#upload" class="small fw-bold"><i class="bi bi-plus-circle me-1"></i>Upload a different CV</a>
            </div>

            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">3. Cover note <span class="small text-muted-emp fw-normal">(optional)</span></h2>
                <label class="form-label" for="cover">Tell {{ $job['company'] }} why you are a good fit</label>
                <textarea id="cover" name="cover_note" class="form-control" rows="5" maxlength="1000" placeholder="Two or three short paragraphs are enough.">{{ old('cover_note') }}</textarea>
                <div class="small text-muted-emp mt-1">Max 1000 characters.</div>
            </div>

            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">4. Quick questions</h2>
                <p class="mb-2"><strong>Availability:</strong> {{ $candidate['availability'] }}</p>
                <p class="mb-0"><strong>Expected salary:</strong> {{ $candidate['expected_salary'] }}</p>
                <div class="small text-muted-emp mt-2">These answers are saved from your profile. <a href="{{ route('candidate.profile') }}">Edit profile</a> before applying if they have changed.</div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="consent" id="consent" value="1" required>
                <label class="form-check-label small" for="consent">I confirm my details are correct and agree that {{ $job['company'] }} can see my profile and selected CV for this application.</label>
                <div class="invalid-feedback">Please tick this box to continue.</div>
            </div>
            <button class="btn btn-emp"><i class="bi bi-send me-1"></i>Submit application</button>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card-emp p-4 mb-4 position-sticky" style="top: 90px;">
            <h2 class="h6 mb-3">Job requirements</h2>
            <div class="small fw-bold text-navy mb-1">Skills this job asks for</div>
            <div class="mb-3">@foreach ($job['skills'] as $s)<span class="skill-chip">{{ $s }}</span> @endforeach</div>
            <div class="small text-muted-emp">Review the listed requirements and decide whether this role is right for you. No automated match score is used.</div>
        </div>
    </div>
</div>
@endsection
