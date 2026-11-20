<footer class="emp-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a href="{{ route('home') }}" class="brand-chip mb-3" aria-label="Employra home">
                    <img src="{{ asset('images/employra-logo.png') }}" alt="Employra — Find, Hire, Grow">
                </a>
                <p class="mb-3" style="max-width: 22rem;">Employra connects job seekers and employers with clear, explainable AI support. People make the decisions.</p>
                <div class="social">
                    <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" aria-label="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2 offset-lg-1">
                <h6>Candidates</h6>
                <a href="{{ route('jobs.index') }}">Find jobs</a><br>
                <a href="{{ route('companies.index') }}">Companies</a><br>
                <a href="#">CV Check</a><br>
                <a href="{{ route('register', ['role' => 'candidate']) }}">Create account</a>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Employers</h6>
                <a href="{{ route('register', ['role' => 'company']) }}">Post a job</a><br>
                <a href="{{ route('register', ['role' => 'company']) }}">Company profile</a><br>
                <a href="#">How ranking works</a><br>
                <a href="{{ route('login') }}">Employer sign in</a>
            </div>
            <div class="col-6 col-lg-3">
                <h6>Company</h6>
                <a href="{{ route('home') }}#about">About Employra</a><br>
                <a href="#">Privacy policy</a><br>
                <a href="#">Terms of use</a><br>
                <a href="#">Contact &amp; support</a>
            </div>
        </div>

        <div class="copy d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} Employra. All rights reserved.</span>
            <span>AI gives suggestions and explanations. Hiring decisions are always made by people.</span>
        </div>
    </div>
</footer>
