{{-- Expects: $status (New, Reviewing, Shortlisted, Interview, Offer, Rejected) --}}
<span class="status {{ $status === 'New' ? 'submitted' : ($status === 'Rejected' ? 'closed' : strtolower($status)) }}">{{ $status }}</span>
