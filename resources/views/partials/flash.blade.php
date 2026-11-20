@if (session('status'))
    <div class="container pt-3" role="status" aria-live="polite">
        <div class="alert alert-{{ session('status_type', 'info') }} alert-dismissible fade show mb-0" role="alert">
            <i class="bi bi-info-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
