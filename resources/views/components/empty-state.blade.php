{{-- Expects: $icon (bootstrap-icons name), $title, $text; optional $actionUrl, $actionLabel --}}
<div class="empty-state card-emp">
    <i class="bi bi-{{ $icon ?? 'inbox' }}"></i>
    <h3 class="h5 mt-3">{{ $title }}</h3>
    <p class="text-muted-emp mb-3 mx-auto" style="max-width: 26rem;">{{ $text }}</p>
    @if (! empty($actionUrl))
        <a href="{{ $actionUrl }}" class="btn btn-outline-navy">{{ $actionLabel ?? 'Try again' }}</a>
    @endif
</div>
