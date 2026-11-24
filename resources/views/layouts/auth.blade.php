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
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap/css/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap-icons/bootstrap-icons.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/employra.css') }}?v={{ filemtime(public_path('css/employra.css')) }}">
    @stack('styles')
</head>
<body class="auth-body">
    <div class="auth-wrap">
        <aside class="auth-side" aria-label="Employra account benefits">
            <div class="auth-side-top">
                <a href="{{ route('home') }}" class="brand-chip" aria-label="Employra home">
                    <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
                </a>
                <span class="auth-top-badge"><i class="bi bi-shield-check"></i> A fairer way to move forward</span>
            </div>

            <div class="auth-copy">
                <span class="eyebrow mb-3"><i class="bi bi-stars"></i> Calm, clear and fair hiring</span>
                <h2 class="mb-3">@yield('side_title', 'Find the right job. Hire the right people.')</h2>
                <p>@yield('side_text', 'One place for candidates and employers, with clear, explainable AI support and people making the decisions.')</p>
                <div class="auth-points">
                    <div class="check"><i class="bi bi-check-circle-fill"></i><span>CV feedback with clear, useful reasons</span></div>
                    <div class="check"><i class="bi bi-check-circle-fill"></i><span>One place to follow every application</span></div>
                    <div class="check"><i class="bi bi-check-circle-fill"></i><span>Your CV stays private until you choose to apply</span></div>
                </div>
            </div>

            <div class="auth-stack" aria-hidden="true">
                <div class="auth-visual auth-visual-1">
                    <div class="auth-visual-bar">
                        <span><i class="bi bi-grid-1x2-fill"></i> Your Employra workspace</span>
                        <span class="auth-live"><i class="bi bi-circle-fill"></i> READY WHEN YOU ARE</span>
                    </div>
                    <div class="auth-visual-profile">
                        <span class="auth-visual-icon"><i class="bi bi-file-earmark-person"></i></span>
                        <div>
                            <strong>Your career, in one place</strong>
                            <span>CVs, applications and next steps</span>
                        </div>
                        <i class="bi bi-arrow-up-right auth-visual-arrow"></i>
                    </div>
                    <div class="auth-visual-steps">
                        <span class="on"><i class="bi bi-check2"></i> Profile</span>
                        <span class="on"><i class="bi bi-check2"></i> Applications</span>
                        <span><i class="bi bi-arrow-right"></i> What's next</span>
                    </div>
                </div>
                <div class="auth-visual auth-visual-2">
                    <span class="auth-mini-check"><i class="bi bi-shield-lock"></i></span>
                    <span><strong>Private by default</strong><small>Share your CV only when you apply.</small></span>
                    <i class="bi bi-check2-circle auth-mini-done"></i>
                </div>
            </div>

            <small class="auth-copyright">&copy; {{ date('Y') }} Employra. All rights reserved.</small>
        </aside>

        <section class="auth-main">
            <div class="auth-top">
                <a href="{{ route('home') }}" class="auth-back"><i class="bi bi-arrow-left"></i> Back to home</a>
            </div>

            <div class="auth-box">
                @include('partials.flash')
                @yield('content')

                <p class="text-center text-muted-emp small mt-4 mb-0"><i class="bi bi-shield-lock me-1"></i>Your details are kept private and secure.</p>
            </div>
        </section>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ filemtime(public_path('vendor/bootstrap/js/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset('js/employra.js') }}?v={{ filemtime(public_path('js/employra.js')) }}"></script>
    @stack('scripts')
</body>
</html>
