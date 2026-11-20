@extends('layouts.employer')
@section('title', 'Company profile')

@section('content')
@php $c = $employer['company']; @endphp
<div class="page-head d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div><h1 class="page-title">Company profile</h1><p class="text-muted-emp mb-0">This is what candidates see on your public page.</p></div>
    @if ($c['status'] === 'Verified')
        <a href="{{ route('companies.show', $c['slug']) }}" class="btn btn-outline-navy btn-sm">View public page</a>
    @endif
</div>

<form action="{{ route('employer.company.update') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">Basics</h2>
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if ($c['logo_url'])
                        <img src="{{ $c['logo_url'] }}" alt="{{ $c['name'] }} logo" class="rounded object-fit-cover" width="72" height="72">
                    @else
                        <div class="co-logo" style="background: {{ $c['color'] }}; width:72px; height:72px; font-size:1.4rem;">{{ $c['initials'] }}</div>
                    @endif
                    <div>
                        <label class="btn btn-outline-navy btn-sm mb-1" for="logo">Upload logo</label>
                        <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp" class="visually-hidden @error('logo') is-invalid @enderror">
                        <div class="small text-muted-emp">JPG, PNG, or WebP; at least 200×200px; up to 2 MB.</div>
                        @error('logo')<div class="small text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="company-name">Company name</label>
                        <input id="company-name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $c['name']) }}" maxlength="120" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="contact-name">Contact name</label>
                        <input id="contact-name" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name', $c['contact_name']) }}" maxlength="120" required>
                        @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="industry">Industry</label>
                        <input id="industry" name="industry" class="form-control @error('industry') is-invalid @enderror" value="{{ old('industry', $c['industry']) }}" maxlength="120" required>
                        @error('industry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="location">Headquarters / location</label>
                        <input id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $c['location']) }}" maxlength="160">
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="company-size">Company size</label>
                        <select id="company-size" name="company_size" class="form-select @error('company_size') is-invalid @enderror">
                            <option value="">Choose a size</option>
                            @foreach (['1–10 employees', '11–50 employees', '51–200 employees', '201–500 employees', '500+ employees'] as $size)
                                <option value="{{ $size }}" @selected(old('company_size', $c['size']) === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                        @error('company_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="website">Website</label>
                        <input id="website" type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $c['website']) }}" maxlength="255" placeholder="https://example.com">
                        @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="registration-number">Registration number</label>
                        <input id="registration-number" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number', $c['registration_number']) }}" maxlength="100">
                        @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="card-emp p-4">
                <h2 class="h5 mb-3">About</h2>
                <div class="mb-0">
                    <label class="form-label" for="description">About the company</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="6" maxlength="5000">{{ old('description', $c['description']) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-emp p-4 mb-4">
                <h2 class="h6 mb-3">Company account</h2>
                <div class="small mb-2"><span class="text-muted-emp">Sign-in email</span><div class="fw-bold text-navy">{{ $c['email'] }}</div></div>
                <div class="d-flex gap-2 align-items-start small">
                    <i class="bi bi-patch-check-fill {{ $c['status'] === 'Verified' ? 'text-success' : 'text-warning' }} fs-5"></i>
                    <div><div class="fw-bold text-navy">{{ $c['status'] }} company</div><div class="text-muted-emp">Company verification is managed by Employra administrators.</div></div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-emp mt-4">Save changes</button>
</form>
@endsection
