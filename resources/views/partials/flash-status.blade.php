@if (session('status'))
    @php
        $statusType = in_array(session('status_type'), ['success', 'warning', 'info', 'danger'], true)
            ? session('status_type')
            : ($fallbackStatusType ?? 'info');
        $isSignInSuccess = session('status') === 'You are now signed in.';
        $companyUser = auth()->guard('company')->user();
        $adminUser = auth()->guard('admin')->user();
        $user = auth()->user() ?? $companyUser ?? $adminUser;
        $role = $companyUser ? 'employer' : ($adminUser ? 'admin' : ($user->role ?? null));
        $name = $companyUser ? ($companyUser->contact_name ?: $companyUser->name) : ($user->name ?? 'there');
        $workspaceMessage = $role === 'candidate'
            ? 'Your job search and application updates are ready.'
            : ($role === 'employer'
                ? 'Your hiring workspace is ready.'
                : ($role === 'admin'
                    ? 'Your administration workspace is ready.'
                    : 'Your workspace is ready.'));
    @endphp

    @if ($isSignInSuccess && $user)
        <div class="alert flash-banner flash-banner-success alert-dismissible fade show" role="status" aria-live="polite" aria-atomic="true">
            <span class="flash-banner-icon" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
            <div class="flash-banner-copy">
                <span class="flash-banner-kicker">Sign-in complete</span>
                <strong>Welcome back, {{ $name }}.</strong>
                <span>{{ $workspaceMessage }}</span>
            </div>
            <span class="flash-banner-secure"><i class="bi bi-shield-check"></i> Secure session</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss welcome message"></button>
        </div>
    @else
        <div class="alert alert-{{ $statusType }} alert-dismissible fade show flash-notice" role="status" aria-live="polite">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Dismiss message"></button>
        </div>
    @endif
@endif
