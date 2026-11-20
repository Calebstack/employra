{{-- Expects: $company (array with slug, name, initials, color, industry, location, short, open_roles) --}}
<a href="{{ route('companies.show', $company['slug']) }}" class="card-emp card-hover company-card">
    <div class="d-flex align-items-center gap-3 mb-3">
        @if (! empty($company['logo_url']))
            <img class="co-logo object-fit-cover" src="{{ $company['logo_url'] }}" alt="" width="48" height="48">
        @else
            <div class="co-logo" style="background: {{ $company['color'] }}" aria-hidden="true">{{ $company['initials'] }}</div>
        @endif
        <div class="min-w-0">
            <h3 class="h6 mb-0 text-navy">{{ $company['name'] }}</h3>
            <div class="text-muted-emp small">{{ $company['industry'] }}</div>
        </div>
    </div>
    <p class="text-muted-emp small mb-3">{{ $company['short'] }}</p>
    <div class="d-flex justify-content-between align-items-center small">
        <span class="text-muted-emp"><i class="bi bi-geo-alt me-1"></i>{{ $company['location'] }}</span>
        <span class="badge-soft @if ($company['open_roles'] > 0) green @else grey @endif">{{ $company['open_roles'] }} open {{ $company['open_roles'] === 1 ? 'role' : 'roles' }}</span>
    </div>
</a>
