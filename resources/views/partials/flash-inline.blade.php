@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-bold mb-1">Please review the following:</div>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    @php $statusType = in_array(session('status_type'), ['success', 'warning', 'info', 'danger'], true) ? session('status_type') : 'success'; @endphp
    <div class="alert alert-{{ $statusType }} alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
