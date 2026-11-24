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
                    @auth
                        @if (auth()->user()->role === 'candidate')
                            <li class="nav-item"><a class="nav-link" href="{{ route('candidate.dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('candidate.applications') }}">My applications</a></li>
                        @elseif (auth()->user()->role === 'employer')
                            <li class="nav-item"><a class="nav-link" href="{{ route('employer.dashboard') }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('employer.jobs') }}">My jobs</a></li>
                        @elseif (auth()->user()->role === 'admin')
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        @endif
                    @endauth
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

                <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center pb-2 pb-lg-0">
                    @auth
                        <div class="dropdown">
                            <button class="user-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Profile menu for {{ auth()->user()->name }}">
                                <span class="avatar" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                                <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down small text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end account-menu">
                                <li class="account-head">
                                    <span class="avatar lg" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-navy text-truncate">{{ auth()->user()->name }}</div>
                                        <div class="small text-muted-emp text-truncate">{{ auth()->user()->email }}</div>
                                    </div>
                                </li>
                                @if (auth()->user()->role === 'candidate')
                                    <li class="account-label">Your account</li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.profile') }}"><i class="bi bi-person"></i>My profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.cvs') }}"><i class="bi bi-file-earmark-text"></i>Resume</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.applications') }}"><i class="bi bi-kanban"></i>My applications</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.saved') }}"><i class="bi bi-bookmark"></i>Saved jobs</a></li>
                                    <li class="account-label">Settings</li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.settings') }}"><i class="bi bi-gear"></i>Account settings</a></li>
                                    <li><a class="dropdown-item" href="{{ route('candidate.settings') }}#notification-settings"><i class="bi bi-bell"></i>Notification settings</a></li>
                                @elseif (auth()->user()->role === 'employer')
                                    <li class="account-label">Your workspace</li>
                                    <li><a class="dropdown-item" href="{{ route('employer.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employer.jobs') }}"><i class="bi bi-briefcase"></i>My jobs</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employer.company') }}"><i class="bi bi-buildings"></i>Company profile</a></li>
                                @elseif (auth()->user()->role === 'admin')
                                    <li class="account-label">Administration</li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i>Platform settings</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i>Sign out</button>
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
