@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="page-head mb-4"><h1 class="page-title">Platform settings</h1><p class="text-muted-emp mb-0">Controls that apply to everyone on Employra.</p></div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-emp p-4 mb-4">
                <h2 class="h5 mb-3">General</h2>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="platform-name">Platform name</label><input id="platform-name" name="platform_name" class="form-control" value="{{ old('platform_name', $settings['platform_name']) }}" required maxlength="120"></div>
                    <div class="col-md-6"><label class="form-label" for="support-email">Support email</label><input id="support-email" name="support_email" type="email" class="form-control" value="{{ old('support_email', $settings['support_email']) }}" required maxlength="255"></div>
                    <div class="col-12"><label class="form-label" for="job-categories">Job categories <span class="text-muted-emp fw-normal">(one per line)</span></label>
                        <textarea id="job-categories" name="job_categories" class="form-control" rows="5" required>{{ old('job_categories', implode("\n", $settings['job_categories'])) }}</textarea></div>
                </div>
            </div>
            <div class="card-emp p-4">
                <h2 class="h5 mb-3">Rules &amp; safety</h2>
                <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="allow-registration" name="allow_registration" value="1" @checked(old('allow_registration', $settings['allow_registration']))><label class="form-check-label" for="allow-registration">Allow new sign-ups</label></div>
                <div class="alert alert-info small mb-0">Employer verification is always required. AI moderation and candidate appeals are not available yet, so they are not shown as configurable settings.</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-emp p-4 mb-4">
                <h2 class="h6 mb-3">AI features</h2>
                <p class="small text-muted-emp mb-0">AI analysis and ranking are preview-only and are not connected to stored application data.</p>
            </div>
            <div class="card-emp p-4">
                <h2 class="h6 mb-3">Admin team</h2>
                @foreach ($admins as $administrator)
                    <div class="d-flex align-items-center gap-2 mb-2"><div class="avatar" style="width:34px;height:34px;font-size:.75rem;">{{ \App\Support\JobPresenter::initials($administrator->name) }}</div><div class="small"><div class="fw-bold text-navy">{{ $administrator->name }}</div><div class="text-muted-emp">{{ $administrator->email }}</div></div></div>
                @endforeach
                <a href="{{ route('admin.admins.create') }}" class="btn btn-outline-navy btn-sm w-100 mt-2"><i class="bi bi-person-plus me-1"></i>Add an admin</a>
            </div>
        </div>
    </div>
    <div class="save-bar"><span class="small text-muted-emp"><i class="bi bi-info-circle me-1"></i>Changes apply to everyone as soon as you save.</span><div class="d-flex gap-2"><button type="reset" class="btn btn-outline-navy">Discard</button><button class="btn btn-emp">Save settings</button></div></div>
</form>
@endsection
