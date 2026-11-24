@extends('layouts.admin')
@section('title', 'Add administrator')

@section('content')
<div class="page-banner slate compact">
    <a href="{{ route('admin.settings') }}" class="small fw-bold text-decoration-none text-white-50"><i class="bi bi-arrow-left me-1"></i>Back to settings</a>
    <h1 class="page-title h3 mt-2">Add administrator</h1>
    <p>Create a separate administrator account with a strong password.</p>
</div>

<form method="POST" action="{{ route('admin.admins.store') }}" class="card-emp p-4" style="max-width:640px;">
    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom"><div class="icon-tile"><i class="bi bi-person-gear"></i></div><div><div class="fw-bold text-navy">Administrator details</div><div class="small text-muted-emp">Passwords need at least 12 characters.</div></div></div>
    @csrf
    <div class="mb-3">
        <label class="form-label" for="admin-name">Name</label>
        <input id="admin-name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="admin-email">Email</label>
        <input id="admin-email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required maxlength="190">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="admin-password">Password</label>
        <input id="admin-password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required minlength="12" autocomplete="new-password">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-4">
        <label class="form-label" for="admin-password-confirmation">Confirm password</label>
        <input id="admin-password-confirmation" name="password_confirmation" type="password" class="form-control" required minlength="12" autocomplete="new-password">
    </div>
    <div class="d-flex flex-wrap gap-2"><button class="btn btn-emp" type="submit">Create administrator</button><a href="{{ route('admin.settings') }}" class="btn btn-outline-navy">Cancel</a></div>
</form>
@endsection
