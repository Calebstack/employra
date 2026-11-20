<header class="emp-header sticky-top">
    <nav class="navbar navbar-expand-lg py-2" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand me-4" href="{{ route('home') }}" aria-label="Employra home">
                <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-4 text-navy"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('jobs.*')) active @endif" href="{{ route('jobs.index') }}">Find Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (request()->routeIs('companies.*')) active @endif" href="{{ route('companies.index') }}">Companies</a>
                    </li>
                    @guest
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Career Tools</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('candidate.cvs') }}"><i class="bi bi-file-earmark-text me-2 text-orange"></i>CV Check<small>Instant, explainable CV feedback</small></a></li>
                                <li><a class="dropdown-item" href="{{ route('candidate.recommended') }}"><i class="bi bi-bullseye me-2 text-orange"></i>Job Matching<small>See roles that fit your skills</small></a></li>
                                <li><a class="dropdown-item" href="{{ route('candidate.applications') }}"><i class="bi bi-kanban me-2 text-orange"></i>Application Tracker<small>Follow every application stage</small></a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register', ['role' => 'company']) }}">For Companies</a>
                        </li>
                    @endguest
                </ul>

                <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center">
                    @auth
                        <div class="dropdown">
                            <button class="user-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Profile menu for {{ auth()->user()->name }}">
                                <span class="avatar" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                                <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down small text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="px-3 py-2">
                                    <div class="fw-bold text-navy">{{ auth()->user()->name }}</div>
                                    <div class="small text-muted-emp">{{ auth()->user()->email }}</div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @if (auth()->user()->role === 'candidate')
                                    <li><a class="dropdown-item" href="{{ route('candidate.profile') }}"><i class="bi bi-person me-2"></i>My profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.cvs') }}"><i class="bi bi-file-earmark-text me-2"></i>Resume</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.settings') }}"><i class="bi bi-gear me-2"></i>Account settings</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.settings') }}#notification-settings"><i class="bi bi-bell me-2"></i>Notification settings</a></li>
                                @elseif (auth()->user()->role === 'employer')
                                    <li><a class="dropdown-item" href="{{ route('employer.company') }}"><i class="bi bi-buildings me-2"></i>Company profile</a></li>
                                @elseif (auth()->user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i>Platform settings</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-emp">Create Account</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
