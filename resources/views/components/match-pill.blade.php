{{-- Expects: $match (0-100). Shows a coloured percentage pill. --}}
<span class="match-pill {{ $match >= 75 ? 'high' : ($match >= 55 ? 'mid' : 'low') }}">
    <i class="bi bi-bullseye"></i>{{ $match }}% match
</span>
