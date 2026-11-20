{{-- Expects: $status (Open, Draft, Closed) --}}
<span class="status {{ $status === 'Open' ? 'offer' : ($status === 'Draft' ? 'submitted' : 'closed') }}">{{ $status }}</span>
