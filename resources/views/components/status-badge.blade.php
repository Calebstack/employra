{{-- Expects: $status (Submitted, Reviewing, Shortlisted, Interview, Offer, Closed, Analyzed, Processing, Failed) --}}
<span class="status {{ in_array($status, ['Rejected', 'Withdrawn'], true) ? 'closed' : strtolower($status) }}">{{ $status }}</span>
