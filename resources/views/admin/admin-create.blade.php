@extends('layouts.admin')
@section('title', 'Add administrator')

@section('content')
<div class="page-head mb-4">
    <a href="{{ route('admin.settings') }}" class="small fw-bold text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Back to settings</a>
    <h1 class="page-title mt-3">Add administrator</h1>
    <p class="text-muted-emp mb-0">Create a separate administrator account with a strong password.</p>
</div>

<form method="POST" action="{{ route('admin.admins.store') }}" class="card-emp p-4" style="max-width:640px;">
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
    <button class="btn btn-emp" type="submit">Create administrator</button>
</form>
@endsection
