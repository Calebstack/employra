<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sign in') — Employra</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employra.css') }}">
    @stack('styles')
</head>
<body>
    <div class="auth-wrap">
        <aside class="auth-side">
            <a href="{{ route('home') }}" class="brand-chip" aria-label="Employra home">
                <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
            </a>

            <div class="my-5">
                <h2 class="mb-3">@yield('side_title', 'Find the right job. Hire the right people.')</h2>
                <p class="mb-4">@yield('side_text', 'One place for candidates and employers, with clear, explainable AI support and people making the decisions.')</p>
                <div class="check"><i class="bi bi-check-circle-fill"></i><span>Free CV feedback with clear reasons, not mystery scores</span></div>
                <div class="check"><i class="bi bi-check-circle-fill"></i><span>Track every application from submitted to offer</span></div>
                <div class="check"><i class="bi bi-check-circle-fill"></i><span>Your CV stays private unless you apply</span></div>
            </div>

            <small class="text-white-50">&copy; {{ date('Y') }} Employra. All rights reserved.</small>
        </aside>

        <section class="auth-main">
            <div class="auth-box">
                <div class="d-lg-none text-center mb-4">
                    <a href="{{ route('home') }}"><img src="{{ asset('images/employra-logo.png') }}" alt="Employra" height="46"></a>
                </div>

                @include('partials.flash')
                @yield('content')

                <p class="text-center mt-4 mb-0 small">
                    <a href="{{ route('home') }}" class="text-muted-emp"><i class="bi bi-arrow-left"></i> Back to home</a>
                </p>
            </div>
        </section>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/employra.js') }}"></script>
    @stack('scripts')
</body>
</html>
