{{-- Expects: $job (array with slug, title, company, company_initials, company_color, location, mode, type, salary, posted, skills) --}}
<a href="{{ route('jobs.show', $job['slug']) }}" class="card-emp card-hover job-card">
    <div class="d-flex gap-3">
        <div class="co-logo" style="background: {{ $job['company_color'] }}" aria-hidden="true">{{ $job['company_initials'] }}</div>

        <div class="flex-grow-1 min-w-0">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="min-w-0">
                    <h3 class="job-title">{{ $job['title'] }}</h3>
                    <div class="text-muted-emp small fw-semibold">{{ $job['company'] }}</div>
                </div>
            </div>

            <div class="meta-row mt-2">
                <span><i class="bi bi-geo-alt"></i>{{ $job['location'] }}</span>
                <span><i class="bi bi-briefcase"></i>{{ $job['type'] }}</span>
                <span><i class="bi bi-laptop"></i>{{ $job['mode'] }}</span>
                <span><i class="bi bi-cash-stack"></i>{{ $job['salary'] }}</span>
            </div>

            <div class="d-flex flex-wrap gap-1 mt-3">
                @foreach (array_slice($job['skills'], 0, 4) as $skill)
                    <span class="skill-chip neutral">{{ $skill }}</span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top small">
        <span class="text-muted-emp"><i class="bi bi-clock me-1"></i>Posted {{ $job['posted'] }}</span>
        <span class="fw-bold text-navy">View details <i class="bi bi-arrow-right"></i></span>
    </div>
</a>
