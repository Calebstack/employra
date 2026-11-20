@extends('layouts.admin')
@section('title', 'Users')

@section('content')
@php $role = request('role', 'all'); @endphp
<div class="page-head d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div><h1 class="page-title">Users</h1><p class="text-muted-emp mb-0">Candidate accounts registered on the platform. Click a row to see details.</p></div>
    <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-download me-1"></i>Export CSV</a>
</div>

<div class="card-emp p-3 mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <ul class="nav nav-pills-emp mb-0 flex-nowrap overflow-auto">
        @foreach (['all' => 'All users', 'candidate' => 'Candidates'] as $k => $l)
            <li class="nav-item"><a class="nav-link {{ $role === $k ? 'active' : '' }}" href="{{ route('admin.users', ['role' => $k]) }}">{{ $l }}</a></li>
        @endforeach
    </ul>
    <form method="GET" action="{{ route('admin.users') }}" class="d-flex gap-2 flex-wrap">
        <input type="hidden" name="role" value="{{ $role }}">
        <select name="status" class="form-select" style="width:auto;" aria-label="Filter by status">
            @foreach (['all' => 'Any status', 'active' => 'Active', 'suspended' => 'Suspended'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status', 'all') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="input-icon" style="min-width:220px;"><i class="bi bi-search"></i><label class="visually-hidden" for="uq">Search users</label><input id="uq" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name or email"></div>
        <button type="submit" class="btn btn-outline-navy btn-sm">Filter</button>
    </form>
</div>

<div class="card-emp overflow-hidden">
    <div class="table-responsive">
    <table class="table table-emp align-middle">
        <thead><tr><th class="ps-4">User</th><th>Role</th><th>Status</th><th>Joined</th><th class="d-none d-md-table-cell">Last seen</th><th></th></tr></thead>
        <tbody>
        @forelse ($users as $u)
            <tr data-row-link="#userPanel-{{ $u['id'] }}" tabindex="0">
                <td class="ps-4"><div class="d-flex align-items-center gap-2"><div class="avatar" style="width:36px;height:36px;font-size:.75rem;">{{ $u['initials'] }}</div><div><div class="fw-bold text-navy">{{ $u['name'] }}</div><div class="small text-muted-emp">{{ $u['email'] }}</div></div></div></td>
                <td><span class="badge-soft {{ $u['role'] === 'Employer' ? 'orange' : '' }}">{{ $u['role'] }}</span></td>
                <td><span class="status {{ $u['status'] === 'Active' ? 'offer' : ($u['status'] === 'Pending' ? 'interview' : 'closed') }}">{{ $u['status'] }}</span></td>
                <td class="small">{{ $u['joined'] }}</td>
                <td class="small d-none d-md-table-cell">{{ $u['last_seen'] }}</td>
                <td class="text-end pe-3" data-stop-row><div class="dropdown"><button class="icon-btn" data-bs-toggle="dropdown" aria-label="User actions"><i class="bi bi-three-dots-vertical"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if ($u['role'] !== 'Admin')
                            <li><a class="dropdown-item" href="#userPanel-{{ $u['id'] }}" data-bs-toggle="offcanvas" data-bs-target="#userPanel-{{ $u['id'] }}">Send message</a></li>
                            <li><form method="POST" action="{{ route('admin.users.reset-password', $u['id']) }}">@csrf<button class="dropdown-item" type="submit">Send password reset link</button></form></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route($u['status'] === 'Suspended' ? 'admin.users.reactivate' : 'admin.users.suspend', $u['id']) }}">
                                    @csrf
                                    <button class="dropdown-item {{ $u['status'] === 'Suspended' ? '' : 'text-danger' }}" type="submit">{{ $u['status'] === 'Suspended' ? 'Reactivate account' : 'Suspend account' }}</button>
                                </form>
                            </li>
                        @endif
                    </ul></div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-0">@include('components.empty-state', ['icon' => 'people', 'title' => 'No users found', 'text' => 'Try a different search or filter.'])</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

@foreach ($users as $u)
    <div class="offcanvas offcanvas-end" tabindex="-1" id="userPanel-{{ $u['id'] }}" aria-labelledby="userPanelLabel-{{ $u['id'] }}">
        <div class="offcanvas-header"><h2 class="h6 mb-0" id="userPanelLabel-{{ $u['id'] }}">User details</h2><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
        <div class="offcanvas-body">
            <div class="text-center mb-4"><div class="avatar mx-auto mb-2" style="width:72px;height:72px;font-size:1.4rem;">{{ $u['initials'] }}</div><div class="h5 mb-0">{{ $u['name'] }}</div><div class="small text-muted-emp">{{ $u['email'] }}</div></div>
            <dl class="row small mb-4"><dt class="col-5">Role</dt><dd class="col-7">{{ $u['role'] }}</dd><dt class="col-5">Status</dt><dd class="col-7">{{ $u['status'] }}</dd><dt class="col-5">Joined</dt><dd class="col-7">{{ $u['joined'] }}</dd><dt class="col-5">Last seen</dt><dd class="col-7">{{ $u['last_seen'] }}</dd>
                <dt class="col-5">Applications</dt><dd class="col-7">{{ $u['applications'] ?? 'Not tracked' }}</dd></dl>
            <a href="{{ route('admin.users.show', $u['id']) }}" class="btn btn-outline-navy w-100 mb-3">View full profile and applications</a>
            <div class="alert alert-info small"><i class="bi bi-shield-check me-1"></i>Candidate profiles and CVs are restricted to administrators. CV downloads are recorded in the activity log.</div>
            @if ($u['role'] !== 'Admin')
                <form method="POST" action="{{ route('admin.users.message', $u['id']) }}" class="mb-2">
                    @csrf
                    <label class="form-label small" for="message-subject-{{ $u['id'] }}">Message subject</label>
                    <input id="message-subject-{{ $u['id'] }}" name="subject" class="form-control mb-2" maxlength="160" required>
                    <label class="form-label small" for="message-body-{{ $u['id'] }}">Message</label>
                    <textarea id="message-body-{{ $u['id'] }}" name="message" class="form-control mb-2" rows="3" minlength="5" maxlength="5000" required></textarea>
                    <button class="btn btn-outline-navy w-100 mb-2" type="submit">Send message</button>
                </form>
                <form method="POST" action="{{ route($u['status'] === 'Suspended' ? 'admin.users.reactivate' : 'admin.users.suspend', $u['id']) }}">
                    @csrf
                    <button class="btn {{ $u['status'] === 'Suspended' ? 'btn-emp' : 'btn-outline-danger' }} w-100" type="submit">{{ $u['status'] === 'Suspended' ? 'Reactivate account' : 'Suspend account' }}</button>
                </form>
            @endif
        </div>
    </div>
@endforeach
@endsection
