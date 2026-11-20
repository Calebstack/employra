@extends('layouts.app')
@section('title', 'Settings')

@section('content')
@php $c = $candidate; @endphp
<div class="page-head mb-4">
    <h1 class="page-title">Settings</h1>
    <p class="text-muted-emp mb-0">Control your account, privacy and notifications.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('candidate.settings.update') }}" method="POST">
            @csrf
            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">Job preferences</h2>
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Roles you want</label><input class="form-control" value="{{ $c['preferred_roles'] }}"></div>
                    <div class="col-md-6"><label class="form-label">Preferred locations</label><input class="form-control" value="{{ $c['preferred_locations'] }}"></div>
                    <div class="col-md-6"><label class="form-label">Expected salary</label><input class="form-control" value="{{ $c['expected_salary'] }}"></div>
                    <div class="col-md-6"><label class="form-label">Availability</label><select class="form-select"><option>Available immediately</option><option>In 2 weeks</option><option>In 1 month</option></select></div>
                </div>
            </div>

            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-1">Privacy</h2>
                <p class="small text-muted-emp">You decide who can see your profile.</p>
                @foreach (['employers_i_apply_to' => ['Only employers I apply to', 'Recommended. Your profile and CV are private until you apply.'], 'all_employers' => ['Employers on Employra', 'Verified employers can find your profile in searches.'], 'hidden' => ['Hidden', 'Nobody can see your profile. You can still apply to jobs.']] as $val => [$t, $d])
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="visibility" id="v-{{ $val }}" value="{{ $val }}" {{ $c['visibility'] === $val ? 'checked' : '' }}>
                        <label class="form-check-label" for="v-{{ $val }}"><span class="fw-bold text-navy">{{ $t }}</span><span class="small text-muted-emp d-block">{{ $d }}</span></label>
                    </div>
                @endforeach
                <div class="small text-muted-emp mt-3"><i class="bi bi-shield-check me-1"></i>Employra never uses your age, gender, religion, tribe or photo to score or rank you.</div>
            </div>

            <div class="card-emp p-4 mb-4" id="notification-settings">
                <h2 class="h5 mb-3">Email notifications</h2>
                @foreach (['Application status updates' => true, 'Interview invitations' => true, 'New jobs that match my profile' => true, 'Tips and product news' => false] as $label => $on)
                    <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="n-{{ $loop->index }}" {{ $on ? 'checked' : '' }}><label class="form-check-label" for="n-{{ $loop->index }}">{{ $label }}</label></div>
                @endforeach
            </div>
            <button class="btn btn-emp">Save settings</button>
        </form>
    </div>

    <div class="col-lg-4">
        <form action="{{ route('candidate.password.update') }}" method="POST" class="card-emp p-4 mb-4 needs-validation" novalidate>
            @csrf
            <h2 class="h6 mb-3">Change password</h2>
            <div class="mb-3"><label class="form-label">Current password</label><input type="password" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">New password</label><input type="password" class="form-control" minlength="8" required></div>
            <div class="mb-3"><label class="form-label">Confirm new password</label><input type="password" class="form-control" required></div>
            <button class="btn btn-outline-navy w-100">Update password</button>
        </form>

        <div class="card-emp p-4" style="border-color:#f3c5c1;">
            <h2 class="h6 text-danger mb-2">Delete account</h2>
            <p class="small text-muted-emp">This removes your profile, CVs and applications permanently.</p>
            <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete my account</button>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border-radius:16px;"><div class="modal-body p-4">
        <h2 class="h5">Delete your account?</h2>
        <p class="text-muted-emp">This cannot be undone. Your CVs and applications will be deleted.</p>
        <div class="d-flex justify-content-end gap-2"><button class="btn btn-outline-navy" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger" data-bs-dismiss="modal">Yes, delete</button></div>
    </div></div></div>
</div>
@endsection
