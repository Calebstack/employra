<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Company verification') — Employra</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employra.css') }}">
</head>
<body class="app-body min-vh-100 d-flex flex-column">
    <header class="app-topbar px-4">
        <a href="{{ route('employer.dashboard') }}" aria-label="Employra employer page">
            <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow" style="max-height:42px;">
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="small text-muted-emp d-none d-sm-inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-navy btn-sm">Sign out</button>
            </form>
        </div>
    </header>
    <main class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
        @yield('content')
    </main>
</body>
</html>
