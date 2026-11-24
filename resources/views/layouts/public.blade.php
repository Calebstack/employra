<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employra — Find · Hire · Grow')</title>
    <meta name="description" content="@yield('description', 'Employra connects job seekers and employers with AI-assisted CV feedback and clear, explainable candidate matching.')">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap/css/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap-icons/bootstrap-icons.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/employra.css') }}?v={{ filemtime(public_path('css/employra.css')) }}">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <a class="visually-hidden-focusable position-absolute p-2 bg-white" href="#main">Skip to content</a>

    @include('partials.public-header')

    <main id="main" class="flex-grow-1">
        @include('partials.flash')
        @yield('content')
    </main>

    @include('partials.public-footer')

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('vendor/bootstrap/js/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset('js/employra.js') }}?v={{ filemtime(public_path('js/employra.js')) }}"></script>
    @stack('scripts')
</body>
</html>
