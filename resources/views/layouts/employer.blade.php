<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Employra</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap/css/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap-icons/bootstrap-icons.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/employra.css') }}?v={{ filemtime(public_path('css/employra.css')) }}">
    @stack('styles')
</head>
<body class="app-body">
    <a class="visually-hidden-focusable position-absolute p-2 bg-white" href="#main">Skip to content</a>

    <div class="app-shell">
        {{-- Desktop sidebar --}}
        <aside class="app-sidebar d-none d-lg-flex" aria-label="Employer navigation">
            @include('partials.employer-sidebar')
        </aside>

        {{-- Mobile sidebar (opens from the menu button) --}}
        <div class="offcanvas offcanvas-start app-offcanvas" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header pb-0">
                <span class="visually-hidden" id="mobileSidebarLabel">Menu</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
            </div>
            <div class="offcanvas-body p-0">
                <div class="app-sidebar" aria-label="Employer navigation">
                    @include('partials.employer-sidebar')
                </div>
            </div>
        </div>

        <div class="app-main">
            <header class="app-topbar">
                <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <form action="{{ route('employer.jobs') }}" method="GET" class="d-none d-md-block flex-grow-1" style="max-width: 420px;" role="search">
                    <label for="topbar-search" class="visually-hidden">Search</label>
                    <div class="input-icon">
                        <i class="bi bi-search"></i>
                        <input id="topbar-search" type="text" name="q" class="form-control" placeholder="Search your jobs or applicants">
                    </div>
                </form>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="{{ route('employer.jobs.create') }}" class="btn btn-emp btn-sm d-none d-sm-inline-flex"><i class="bi bi-plus-lg me-1"></i>Post a job</a>

                    <div class="dropdown">
                        <button class="icon-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notifications">
                            <i class="bi bi-bell"></i><span class="dot"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end notif-menu">
                            <div class="d-flex justify-content-between align-items-center px-2 py-1">
                                <strong class="text-navy">Notifications</strong>
                                <a href="#" class="small fw-bold">Mark all read</a>
                            </div>
                            @foreach ($notifications ?? [] as $n)
                                <a href="#" class="notif-item text-decoration-none">
                                    <div class="icon-tile {{ $n['tone'] === 'orange' ? 'orange' : ($n['tone'] === 'green' ? 'green' : '') }}"><i class="bi bi-{{ $n['icon'] }}"></i></div>
                                    <div>
                                        <div class="small fw-bold text-navy">{{ $n['text'] }}</div>
                                        <div class="small text-muted-emp">{{ $n['time'] }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="user-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar" aria-hidden="true">{{ $employer['initials'] ?? 'TB' }}</span>
                            <span class="d-none d-sm-inline">{{ $employer['first_name'] ?? 'Account' }}</span>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end account-menu">
                            <li class="account-head">
                                <span class="avatar lg" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                                <div class="min-w-0">
                                    <div class="fw-bold text-navy text-truncate">@auth {{ auth()->user()->name }} @else Account @endauth</div>
                                    <div class="small text-muted-emp text-truncate">@auth {{ auth()->user()->email }} @endauth</div>
                                </div>
                            </li>
                            <li class="account-label">Your workspace</li>
                            <li><a class="dropdown-item" href="{{ route('employer.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('employer.jobs') }}"><i class="bi bi-briefcase"></i>My jobs</a></li>
                            <li><a class="dropdown-item" href="{{ route('employer.company') }}"><i class="bi bi-buildings"></i>Company profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('employer.company') }}"><i class="bi bi-gear"></i>Account settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i>Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main id="main" class="app-content">
                @include('partials.flash-inline')
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('vendor/bootstrap/js/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset('js/employra.js') }}?v={{ filemtime(public_path('js/employra.js')) }}"></script>
    @stack('scripts')
</body>
</html>
