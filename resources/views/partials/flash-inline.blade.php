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

@include('partials.flash-status', ['fallbackStatusType' => 'info'])
