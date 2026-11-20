<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Employra Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employra.css') }}">
    @stack('styles')
</head>
<body class="app-body">
    <a class="visually-hidden-focusable position-absolute p-2 bg-white" href="#main">Skip to content</a>

    <div class="app-shell">
        {{-- Desktop sidebar --}}
        <aside class="app-sidebar d-none d-lg-flex" aria-label="Admin navigation">
            @include('partials.admin-sidebar')
        </aside>

        {{-- Mobile sidebar (opens from the menu button) --}}
        <div class="offcanvas offcanvas-start app-offcanvas" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header pb-0">
                <span class="visually-hidden" id="mobileSidebarLabel">Menu</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
            </div>
            <div class="offcanvas-body p-0">
                <div class="app-sidebar" aria-label="Admin navigation">
                    @include('partials.admin-sidebar')
                </div>
            </div>
        </div>

        <div class="app-main">
            <header class="app-topbar">
                <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open menu">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <form action="{{ route('admin.users') }}" method="GET" class="d-none d-md-block flex-grow-1" style="max-width: 420px;" role="search">
                    <label for="topbar-search" class="visually-hidden">Search</label>
                    <div class="input-icon">
                        <i class="bi bi-search"></i>
                        <input id="topbar-search" type="text" name="q" class="form-control" placeholder="Search users">
                    </div>
                </form>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="{{ route('admin.companies', ['status' => 'Pending']) }}" class="btn btn-emp btn-sm d-none d-sm-inline-flex"><i class="bi bi-patch-check me-1"></i>Review companies</a>

                    <div class="dropdown">
                        <button class="user-menu" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar" aria-hidden="true">{{ $admin['initials'] ?? 'EA' }}</span>
                            <span class="d-none d-sm-inline">{{ $admin['first_name'] ?? 'Admin' }}</span>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius:14px; padding:.5rem;">
                            <li><a class="dropdown-item rounded-2 fw-semibold" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i>Platform settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 fw-semibold text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
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

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/employra.js') }}"></script>
    @stack('scripts')
</body>
</html>
