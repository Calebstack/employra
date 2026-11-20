<?php

/*
|--------------------------------------------------------------------------
| Employra GUI preview routes  (TEMPORARY — delete when the backend is real)
|--------------------------------------------------------------------------
|
| These routes only exist so every screen can be opened and clicked through
| using sample data from App\Support\MockData. Each closure passes the views
| the same variable names your real controllers should pass later.
|
| Load this file at the TOP of routes/web.php so "/" shows the Employra
| landing page instead of Laravel's default welcome page:
|
|     require __DIR__.'/gui-preview.php';
|
*/

use App\Support\CandidateMockData as Cand;
use App\Support\EmployerMockData as Emp;
use App\Support\MockData;
use App\Support\CandidateCvPresenter;
use App\Support\CandidateProfilePresenter;
use App\Support\NigerianStates;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Models\AdminActivityLog;
use App\Models\Report;
use App\Models\User;
use App\Support\JobPresenter;
use App\Support\JobApplicationPresenter;
use App\Support\CompanyPresenter;
use App\Support\PlatformSettings;
use App\Support\AdminAudit;
use App\Http\Controllers\CandidateApplicationController;
use App\Http\Controllers\CandidateCvController;
use App\Http\Controllers\CandidateInterviewResponseController;
use App\Http\Controllers\CandidateOnboardingController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\EmployerApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// ---- Public pages ---------------------------------------------------------

Route::get('/', function (Request $request) {
    $user = $request->user();
    if ($user?->role === 'employer') {
        return redirect()->route('employer.dashboard');
    }
    if ($user?->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user?->role === 'candidate') {
        if ($user->candidateProfile?->onboarding_completed_at === null) {
            return redirect()->route('candidate.onboarding');
        }

        return redirect()->route('jobs.index');
    }

    return view('public.home', [
        'stats' => [
            ['value' => number_format(Job::where('status', 'open')->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))->count()), 'label' => 'Open roles'],
            ['value' => number_format(Company::where('status', 'verified')->count()), 'label' => 'Verified companies'],
            ['value' => number_format(User::where('role', 'candidate')->count()), 'label' => 'Candidate accounts'],
            ['value' => number_format(JobApplication::count()), 'label' => 'Applications tracked'],
        ],
        'featuredJobs' => Job::with('company')
            ->where('status', 'open')
            ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Job $job) => JobPresenter::forPublic($job))
            ->all(),
        'featuredCompanies' => Company::withCount(['jobs' => fn ($jobs) => $jobs->where('status', 'open')])
            ->where('status', 'verified')
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (Company $company) => CompanyPresenter::forPublic($company))
            ->all(),
        'states' => NigerianStates::options(),
    ]);
})->name('home');

Route::get('/jobs', function (Request $request) {
    $validator = Validator::make($request->query(), [
        'state' => ['nullable', 'string', Rule::in(array_keys(NigerianStates::options()))],
        'salary_min' => ['nullable', 'integer', 'min:0', 'max:10000000'],
        'salary_max' => ['nullable', 'integer', 'min:0', 'max:10000000'],
    ]);
    $validator->after(function ($validator) use ($request): void {
        $min = $request->query('salary_min');
        $max = $request->query('salary_max');
        if (is_numeric($min) && is_numeric($max) && (int) $min > (int) $max) {
            $validator->errors()->add('salary_max', 'The maximum salary must be at least the minimum salary.');
        }
    });
    if ($validator->fails()) {
        return redirect()->route('jobs.index')
            ->withErrors($validator)
            ->withInput($request->query());
    }

    $storedJobs = Job::with('company')
        ->where('status', 'open')
        ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
        ->latest()
        ->get()
        ->map(fn (Job $job) => JobPresenter::forPublic($job))
        ->all();

    return view('public.jobs.index', [
        'jobs' => JobPresenter::filter($storedJobs, $request->query()),
        'categories' => PlatformSettings::get('job_categories', MockData::categories()),
        'types' => MockData::jobTypes(),
        'modes' => MockData::workModes(),
        'levels' => MockData::levels(),
        'states' => NigerianStates::options(),
    ]);
})->name('jobs.index');

Route::get('/jobs/{slug}', function (string $slug) {
    $storedJob = Job::with('company')
        ->where('slug', $slug)
        ->where('status', 'open')
        ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
        ->firstOrFail();
    $job = JobPresenter::forPublic($storedJob);
    $storedCompany = Company::withCount(['jobs' => fn ($jobs) => $jobs->where('status', 'open')])
        ->whereKey($storedJob->company_id)
        ->firstOrFail();
    $company = CompanyPresenter::forPublic($storedCompany);
    $similar = Job::with('company')
        ->where('status', 'open')
        ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
        ->where('id', '!=', $storedJob->id)
        ->where('category', $job['category'])
        ->latest()
        ->limit(3)
        ->get()
        ->map(fn (Job $storedJob) => JobPresenter::forPublic($storedJob))
        ->all();

    return view('public.jobs.show', [
        'job' => $job,
        'company' => $company,
        'similar' => array_slice($similar, 0, 3),
    ]);
})->name('jobs.show');

Route::post('/jobs/{slug}/report', function (Request $request, string $slug) {
    $job = Job::with('company')
        ->where('slug', $slug)
        ->where('status', 'open')
        ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
        ->firstOrFail();
    $validated = $request->validate([
        'reason' => ['required', Rule::in(['Asks candidates for payment', 'Misleading information', 'Discrimination', 'Other'])],
        'details' => ['required', 'string', 'min:10', 'max:3000'],
    ]);

    Report::create([
        'target_type' => 'job',
        'target_id' => $job->id,
        'target_label' => $job->title.' at '.$job->company->name,
        'reason' => $validated['reason'],
        'details' => $validated['details'],
        'reported_by' => $request->user()->id,
        'reporter_name' => $request->user()->name,
        'reporter_email' => $request->user()->email,
        'severity' => $validated['reason'] === 'Asks candidates for payment' ? 'high' : 'medium',
        'status' => 'open',
    ]);

    return back()->with('status', 'Thank you. Your report has been sent to Employra for review.');
})->middleware(['auth', 'role:candidate', 'throttle:10,60'])->name('jobs.report');

Route::get('/companies', function (Request $request) {
    $query = Company::withCount(['jobs' => fn ($jobs) => $jobs->where('status', 'open')])
        ->where('status', 'verified');
    $search = trim((string) $request->query('q', ''));
    if ($search !== '') {
        $query->where(fn ($companies) => $companies->where('name', 'like', '%'.$search.'%')
            ->orWhere('location', 'like', '%'.$search.'%'));
    }
    if ($request->filled('industry')) {
        $query->where('industry', $request->query('industry'));
    }

    return view('public.companies.index', [
        'companies' => $query->latest()->get()->map(fn (Company $company) => CompanyPresenter::forPublic($company))->all(),
        'industries' => Company::where('status', 'verified')->distinct()->orderBy('industry')->pluck('industry')->all(),
    ]);
})->name('companies.index');

Route::get('/companies/{slug}', function (string $slug) {
    $storedCompany = Company::withCount(['jobs' => fn ($jobs) => $jobs->where('status', 'open')])
        ->where('slug', $slug)
        ->where('status', 'verified')
        ->firstOrFail();
    $company = CompanyPresenter::forPublic($storedCompany);
    $jobs = Job::with('company')
        ->where('company_id', $storedCompany->id)
        ->where('status', 'open')
        ->latest()
        ->get()
        ->map(fn (Job $job) => JobPresenter::forPublic($job))
        ->all();

    return view('public.companies.show', [
        'company' => $company,
        'jobs' => $jobs,
    ]);
})->name('companies.show');

Route::post('/companies/{slug}/report', function (Request $request, string $slug) {
    $company = Company::where('slug', $slug)->where('status', 'verified')->firstOrFail();
    $validated = $request->validate([
        'reason' => ['required', Rule::in(['Fraudulent company information', 'Impersonation', 'Discrimination', 'Other'])],
        'details' => ['required', 'string', 'min:10', 'max:3000'],
    ]);

    Report::create([
        'target_type' => 'company',
        'target_id' => $company->id,
        'target_label' => $company->name,
        'reason' => $validated['reason'],
        'details' => $validated['details'],
        'reported_by' => $request->user()->id,
        'reporter_name' => $request->user()->name,
        'reporter_email' => $request->user()->email,
        'severity' => 'medium',
        'status' => 'open',
    ]);

    return redirect()->route('companies.show', $company->slug)
        ->with('status', 'Your company report was submitted for review.')
        ->with('status_type', 'success');
})->middleware(['auth', 'role:candidate', 'throttle:10,60'])->name('companies.report');

// ---- Authentication screens and account actions ---------------------------

Route::view('/login', 'public.auth.login')->middleware('guest')->name('login');
Route::get('/register', function () {
    abort_unless(PlatformSettings::get('allow_registration', true), 403, 'New account registration is currently closed.');

    return view('public.auth.register');
})->middleware('guest')->name('register');
Route::view('/forgot-password', 'public.auth.forgot-password')->name('password.request');

Route::post('/login', [LoginController::class, 'store'])->middleware(['guest', 'throttle:login'])->name('login.submit');
Route::post('/register', [RegisterController::class, 'store'])->middleware(['guest', 'throttle:10,60'])->name('register.submit');
Route::post('/logout', [LogoutController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/forgot-password', fn (Request $request) => back()->withInput($request->except('email'))->with('status', 'Password reset is not available yet.')->with('status_type', 'warning'))->name('password.email');


// ---- Candidate area (sign-in required in the real app) -------------------
// Every candidate view receives $candidate, $notifications and $sidebarCounts.

$shared = fn (array $data = []) => array_merge([
    'candidate' => CandidateProfilePresenter::forCandidate(
        request()->user(),
        request()->user()->candidateProfile()->firstOrCreate([])
    ),
    'notifications' => [],
    'sidebarCounts' => [
        'applications' => JobApplication::where('user_id', request()->user()->id)->count(),
    ],
], $data);
$candidateApplications = fn (User $candidate) => JobApplication::with(['job.company', 'cv', 'statusHistory', 'interviews'])
    ->where('user_id', $candidate->id)
    ->latest()
    ->get()
    ->map(fn (JobApplication $application) => JobApplicationPresenter::forCandidate($application))
    ->all();
$availableJobs = static fn (User $candidate) => Job::with('company')
    ->where('status', 'open')
    ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
    ->whereDoesntHave('applications', fn ($applications) => $applications->where('user_id', $candidate->id))
    ->latest()
    ->limit(6)
    ->get()
    ->map(fn (Job $job) => JobPresenter::forPublic($job))
    ->all();

Route::get('/candidate/onboarding', [CandidateOnboardingController::class, 'create'])
    ->middleware('auth')
    ->name('candidate.onboarding');
Route::post('/candidate/onboarding', [CandidateOnboardingController::class, 'store'])
    ->middleware(['auth', 'throttle:10,60'])
    ->name('candidate.onboarding.store');

Route::get('/candidate', function (Request $request) use ($shared, $candidateApplications, $availableJobs) {
    $profile = $request->user()->candidateProfile()->firstOrCreate([]);
    return view('candidate.dashboard', $shared([
        'applications' => $candidateApplications($request->user()),
        'recentJobs' => $availableJobs($request->user()),
        'cvs' => CandidateCvPresenter::forProfile($profile),
    ]));
})->middleware(['auth:web', 'role:candidate'])->name('candidate.dashboard');

Route::get('/candidate/recommended', function (Request $request) use ($shared, $availableJobs) {
    return view('candidate.recommended', $shared(['jobs' => $availableJobs($request->user())]));
})->middleware(['auth:web', 'role:candidate'])->name('candidate.recommended');
Route::get('/candidate/saved', fn () => view('candidate.saved', $shared(['jobs' => []])))
    ->middleware(['auth:web', 'role:candidate'])->name('candidate.saved');

Route::get('/candidate/applications', function (Request $request) use ($shared) {
    $apps = JobApplication::with(['job.company', 'cv', 'statusHistory', 'interviews'])
        ->where('user_id', $request->user()->id)
        ->latest()
        ->get()
        ->map(fn (JobApplication $application) => JobApplicationPresenter::forCandidate($application))
        ->all();
    $filter = $request->query('status', 'all');
    $apps = array_values(array_filter($apps, fn ($a) => match ($filter) {
        'active' => ! ($a['is_closed'] ?? in_array($a['status'], ['Closed', 'Rejected', 'Withdrawn'], true)),
        'interview' => $a['status'] === 'Interview',
        'closed' => $a['is_closed'] ?? in_array($a['status'], ['Closed', 'Rejected', 'Withdrawn'], true),
        default => true,
    }));

    return view('candidate.applications.index', $shared(['applications' => $apps]));
})->middleware(['auth:web', 'role:candidate'])->name('candidate.applications');

Route::get('/candidate/applications/{id}', function (Request $request, int $id) use ($shared) {
    $application = JobApplication::with(['job.company', 'cv', 'statusHistory', 'interviews'])
        ->where('user_id', $request->user()->id)
        ->find($id);
    $application = $application instanceof JobApplication ? JobApplicationPresenter::forCandidate($application) : null;
    abort_if($application === null, 404);

    return view('candidate.applications.show', $shared(['application' => $application]));
})->middleware(['auth:web', 'role:candidate'])->whereNumber('id')->name('candidate.applications.show');
Route::post('/candidate/applications/{id}/withdraw', [CandidateApplicationController::class, 'withdraw'])
    ->middleware(['auth:web', 'role:candidate'])
    ->whereNumber('id')
    ->name('candidate.applications.withdraw');
Route::post('/candidate/interviews/{id}/response', [CandidateInterviewResponseController::class, 'update'])
    ->middleware(['auth:web', 'role:candidate', 'throttle:10,60'])
    ->whereNumber('id')
    ->name('candidate.interviews.response');

Route::get('/candidate/cvs', function (Request $request) use ($shared) {
    $profile = $request->user()->candidateProfile;
    $cvs = $profile ? CandidateCvPresenter::forProfile($profile) : [];

    return view('candidate.cvs.index', $shared(['cvs' => $cvs]));
})->middleware(['auth:web', 'role:candidate'])->name('candidate.cvs');
Route::get('/candidate/cvs/{cv}/download', [CandidateCvController::class, 'download'])
    ->middleware('auth')
    ->whereNumber('cv')
    ->name('candidate.cvs.download');
Route::get('/candidate/cvs/{id}', fn () => abort(404))
    ->middleware(['auth:web', 'role:candidate'])
    ->whereNumber('id')->name('candidate.cvs.show');

Route::get('/candidate/profile', [CandidateProfileController::class, 'show'])
    ->middleware(['auth:web', 'role:candidate'])
    ->name('candidate.profile');
Route::get('/candidate/profile/photo', [CandidateProfileController::class, 'photo'])
    ->middleware(['auth:web', 'role:candidate'])
    ->name('candidate.profile.photo');
Route::post('/candidate/profile', [CandidateProfileController::class, 'update'])
    ->middleware(['auth:web', 'role:candidate', 'throttle:10,60'])
    ->name('candidate.profile.update');
Route::get('/candidate/settings', fn () => view('candidate.settings', $shared()))
    ->middleware(['auth:web', 'role:candidate'])->name('candidate.settings');

Route::get('/jobs/{slug}/apply', function (Request $request, string $slug) use ($shared) {
    $storedJob = Job::with('company')
        ->where('slug', $slug)
        ->where('status', 'open')
        ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
        ->firstOrFail();
    $job = JobPresenter::forPublic($storedJob);
    $user = $request->user();
    abort_unless($user?->role === 'candidate', 403);
    $profile = $user->candidateProfile()->firstOrCreate([]);
    $cvs = $profile ? CandidateCvPresenter::forProfile($profile) : [];
    $candidate = CandidateProfilePresenter::forCandidate($user, $profile);

    return view('candidate.apply', $shared([
        'job' => $job,
        'cvs' => $cvs,
        'candidate' => $candidate,
    ]));
})->middleware(['auth:web', 'role:candidate'])->name('jobs.apply');

$candidateDemo = fn (string $msg) => fn () => back()->with('status', 'Demo only: '.$msg);
Route::post('/jobs/{slug}/apply', [CandidateApplicationController::class, 'store'])
    ->middleware(['auth:web', 'role:candidate'])
    ->name('jobs.apply.submit');
Route::post('/candidate/cvs/upload', [CandidateCvController::class, 'store'])
    ->middleware(['auth', 'throttle:10,60'])
    ->name('candidate.cvs.upload');
Route::post('/candidate/settings', $candidateDemo('settings would be saved here.'))->name('candidate.settings.update');
Route::post('/candidate/password', $candidateDemo('your password would be updated here.'))->name('candidate.password.update');


// ---- Employer area ---------------------------------------------------------
// Every employer view receives $employer, $notifications and $sidebarCounts.

Route::middleware(['auth:company', 'role:employer'])->group(function () {
    $initials = static function (string $name): string {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return Str::upper(substr($parts[0] ?? '', 0, 1).(count($parts) > 1 ? substr($parts[count($parts) - 1], 0, 1) : ''));
    };

    $emp = function (array $data = []) use ($initials): array {
        $company = request()->user();
        abort_unless($company instanceof Company, 403);

        $employer = Emp::employer();
        $employer['name'] = $company->contact_name ?: $company->name;
        $employer['first_name'] = Str::before($employer['name'], ' ');
        $employer['initials'] = $initials($employer['name']);
        $employer['role'] = 'Employer';
        $employer['email'] = $company->email;
        $employer['company'] = [
            'name' => $company->name,
            'slug' => $company->slug,
            'industry' => $company->industry,
            'status' => ucfirst($company->status),
            'color' => '#1558d6',
            'initials' => $initials($company->name),
            'size' => $company->company_size ?? '',
            'location' => $company->location ?? '',
            'founded' => '',
            'website' => $company->website ?? '',
            'description' => $company->description ?? '',
            'registration_number' => $company->registration_number ?? '',
            'contact_name' => $company->contact_name ?? '',
            'email' => $company->email ?? '',
            'logo_path' => $company->logo_path,
            'logo_url' => $company->logo_path ? Storage::disk('public')->url($company->logo_path) : null,
        ];

        return array_merge([
            'employer' => $employer,
            'notifications' => [],
            'sidebarCounts' => [
                'new' => JobApplication::where('status', 'submitted')
                    ->whereHas('job', fn ($query) => $query->where('company_id', $company->id))
                    ->count(),
            ],
        ], $data);
    };
    $companyInterviews = static function (Company $company) use ($initials): array {
        return JobInterview::with(['application.candidate', 'application.job'])
            ->whereHas('application.job', fn ($jobs) => $jobs->where('company_id', $company->id))
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn (JobInterview $interview) => [
                'id' => $interview->id,
                'applicant_id' => $interview->application->id,
                'name' => $interview->application->candidate->name,
                'initials' => $initials($interview->application->candidate->name),
                'job' => $interview->application->job->title,
                'date' => $interview->scheduled_at->format('D, j M Y'),
                'time' => $interview->scheduled_at->format('g:i A'),
                'mode' => match ($interview->format) {
                    'video' => 'Video call',
                    'phone' => 'Phone call',
                    'in_person' => 'In person',
                    default => 'Interview',
                },
                'details' => $interview->meeting_details,
                'response_note' => $interview->response_note,
                'status' => match ($interview->candidate_response) {
                    'confirmed' => 'Confirmed',
                    'reschedule_requested' => 'Reschedule requested',
                    default => 'Awaiting response',
                },
                'upcoming' => $interview->status === 'scheduled' && $interview->scheduled_at->isFuture(),
                'this_week' => $interview->status === 'scheduled'
                    && $interview->scheduled_at->between(now(), now()->addWeek()),
            ])
            ->all();
    };

    Route::get('/employer/company', function () use ($emp) {
        return view('employer.company', $emp());
    })->name('employer.company');

    Route::post('/employer/company', function (Request $request) {
        $company = $request->user();
        abort_unless($company instanceof Company, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'contact_name' => ['required', 'string', 'max:120'],
            'industry' => ['required', 'string', 'max:120'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:160'],
            'company_size' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:5000'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=200,min_height=200'],
        ]);

        $oldLogoPath = $company->logo_path;
        $newLogoPath = null;
        if ($request->hasFile('logo')) {
            $newLogoPath = $request->file('logo')->store('company-logos', 'public');
            if ($newLogoPath === false) {
                throw new \RuntimeException('The company logo could not be uploaded.');
            }
        }

        try {
            $company->forceFill([
                'name' => $validated['name'],
                'contact_name' => $validated['contact_name'],
                'industry' => $validated['industry'],
                'website' => $validated['website'] ?? null,
                'location' => $validated['location'] ?? null,
                'company_size' => $validated['company_size'] ?? null,
                'description' => $validated['description'] ?? null,
                'registration_number' => $validated['registration_number'] ?? null,
                'logo_path' => $newLogoPath ?? $oldLogoPath,
            ])->save();
        } catch (\Throwable $exception) {
            if ($newLogoPath !== null && ! Storage::disk('public')->delete($newLogoPath)) {
                throw new \RuntimeException('Company profile save failed and the uploaded logo could not be removed.', 0, $exception);
            }

            throw $exception;
        }

        if ($newLogoPath !== null && $oldLogoPath !== null
            && Storage::disk('public')->exists($oldLogoPath)
            && ! Storage::disk('public')->delete($oldLogoPath)) {
            throw new \RuntimeException('Company profile was saved, but the previous logo could not be removed.');
        }

        return redirect()->route('employer.company')
            ->with('status', 'Company profile updated.')
            ->with('status_type', 'success');
    })->name('employer.company.update');

    Route::get('/employer', function (Request $request) use ($emp, $companyInterviews) {
        $company = $request->user();
        abort_unless($company instanceof Company, 403);

        if ($company->status !== 'verified') {
            return view('employer.awaiting-verification', ['company' => $company]);
        }

        $jobs = Job::with('company')->withCount('applications')->where('company_id', $company->id)->latest()->get();
        $jobCards = $jobs->map(fn (Job $job) => JobPresenter::forEmployer($job))->all();
        $openJobs = $jobs->where('status', 'open');
        $applications = JobApplication::with(['candidate.candidateProfile', 'job', 'cv', 'interviews'])
            ->whereHas('job', fn ($query) => $query->where('company_id', $company->id))
            ->latest()
            ->get();
        $applicantCards = $applications->map(fn (JobApplication $application) => JobApplicationPresenter::forCompany($application))->all();
        $pipeline = [
            'New' => $applications->where('status', 'submitted')->count(),
            'Reviewing' => $applications->where('status', 'reviewing')->count(),
            'Shortlisted' => $applications->where('status', 'shortlisted')->count(),
            'Interview' => $applications->where('status', 'interview')->count(),
            'Rejected' => $applications->where('status', 'rejected')->count(),
        ];
        $interviews = $companyInterviews($company);

        return view('employer.dashboard', $emp([
            'stats' => [
                'open' => $openJobs->count(),
                'applicants' => $applications->count(),
                'new' => $pipeline['New'],
                'interviews' => count(array_filter(
                    $interviews,
                    static fn (array $interview): bool => $interview['this_week']
                )),
            ],
            'applicants' => $applicantCards,
            'jobs' => $jobCards,
            'interviews' => array_values(array_filter($interviews, static fn (array $interview): bool => $interview['upcoming'])),
            'pipeline' => $pipeline,
        ]));
    })->name('employer.dashboard');

    Route::middleware('verified-employer')->group(function () use ($emp, $companyInterviews) {
        Route::get('/employer/jobs', function (Request $request) use ($emp) {
            $company = $request->attributes->get('employerCompany');
            $jobs = Job::with('company')->withCount('applications')->where('company_id', $company->id)->latest()->get()
                ->map(fn (Job $job) => JobPresenter::forEmployer($job))
                ->all();

            return view('employer.jobs.index', $emp(['jobs' => $jobs]));
        })->name('employer.jobs');

        Route::get('/employer/jobs/create', fn () => view('employer.jobs.create', $emp([
            'categories' => PlatformSettings::get('job_categories', MockData::categories()), 'levels' => MockData::levels(), 'types' => MockData::jobTypes(), 'modes' => MockData::workModes(),
        ])))->name('employer.jobs.create');
        Route::post('/employer/jobs', function (Request $request) {
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:160'],
                'category' => ['required', Rule::in(PlatformSettings::get('job_categories', MockData::categories()))],
                'experience_level' => ['required', Rule::in(MockData::levels())],
                'job_type' => ['required', Rule::in(MockData::jobTypes())],
                'work_mode' => ['required', Rule::in(MockData::workModes())],
                'location' => ['required', 'string', 'max:120'],
                'salary_min' => ['nullable', 'integer', 'min:0', 'max:10000000'],
                'salary_max' => ['nullable', 'integer', 'min:0', 'max:10000000'],
                'deadline' => ['nullable', 'date', 'after_or_equal:today'],
                'summary' => ['required', 'string', 'max:5000'],
                'responsibilities' => ['nullable', 'string', 'max:10000'],
                'benefits' => ['nullable', 'string', 'max:5000'],
                'required_skills' => ['nullable', 'array', 'max:30'],
                'required_skills.*' => ['required', 'string', 'max:80', 'distinct'],
                'preferred_skills' => ['nullable', 'array', 'max:30'],
                'preferred_skills.*' => ['required', 'string', 'max:80', 'distinct'],
                'min_experience_years' => ['required', 'integer', 'min:0', 'max:60'],
                'minimum_education' => ['nullable', Rule::in(['OND / NCE', 'HND / B.Sc.', 'M.Sc.'])],
                'notify_applicants' => ['nullable', 'boolean'],
                'status' => ['required', Rule::in(['open', 'draft'])],
            ]);
            $validator->after(function ($validator) use ($request): void {
                $min = $request->input('salary_min');
                $max = $request->input('salary_max');
                if (is_numeric($min) && is_numeric($max) && (int) $min > (int) $max) {
                    $validator->errors()->add('salary_max', 'The maximum salary must be at least the minimum salary.');
                }
            });
            $validated = $validator->validate();
            $company = $request->attributes->get('employerCompany');
            $slug = Str::slug($validated['title'].'-'.$company->slug);
            if (Job::where('slug', $slug)->exists()) {
                $slug .= '-'.Str::lower(Str::random(6));
            }

            Job::create([
                'company_id' => $company->id,
                'title' => $validated['title'],
                'slug' => $slug,
                'category' => $validated['category'],
                'experience_level' => $validated['experience_level'],
                'job_type' => $validated['job_type'],
                'work_mode' => $validated['work_mode'],
                'location' => $validated['location'],
                'salary_min' => $validated['salary_min'] ?? null,
                'salary_max' => $validated['salary_max'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'summary' => $validated['summary'],
                'responsibilities' => $validated['responsibilities'] ?? null,
                'benefits' => $validated['benefits'] ?? null,
                'required_skills' => array_values(array_unique(array_map('trim', $validated['required_skills'] ?? []))),
                'preferred_skills' => array_values(array_unique(array_map('trim', $validated['preferred_skills'] ?? []))),
                'min_experience_years' => $validated['min_experience_years'],
                'minimum_education' => $validated['minimum_education'] ?? null,
                'notify_applicants' => $request->boolean('notify_applicants'),
                'status' => $validated['status'],
            ]);

            return redirect()->route('employer.jobs')->with('status', $validated['status'] === 'open' ? 'Your job has been published.' : 'Your job draft has been saved.');
        })->name('employer.jobs.store');

        Route::get('/employer/jobs/{id}', function (Request $request, int $id) use ($emp) {
            $jobModel = Job::with('company')->withCount('applications')
                ->where('company_id', $request->attributes->get('employerCompany')->id)
                ->findOrFail($id);
            $job = JobPresenter::forEmployer($jobModel);
            $weights = [];
            foreach (['skills', 'experience', 'education', 'keywords'] as $k) {
                if ($request->has('w_'.$k)) {
                    $weights[$k] = $request->query('w_'.$k);
                }
            }
            $criteria = array_map(fn ($c) => $weights ? array_merge($c, ['weight' => (int) ($weights[$c['key']] ?? $c['weight'])]) : $c, Emp::criteria());

            $applications = JobApplication::with(['candidate.candidateProfile', 'job', 'cv', 'interviews'])
                ->where('job_id', $jobModel->id)
                ->latest()
                ->get();
            $stage = $request->query('stage', 'all');
            $stageValues = [
                'New' => 'New',
                'Reviewing' => 'Reviewing',
                'Shortlisted' => 'Shortlisted',
                'Interview' => 'Interview',
                'Rejected' => 'Rejected',
            ];
            $applicants = $applications->map(fn (JobApplication $application) => JobApplicationPresenter::forCompany($application))
                ->when(isset($stageValues[$stage]), fn ($items) => $items->where('status', JobApplicationPresenter::statusLabel($stageValues[$stage])))
                ->values()
                ->all();

            return view('employer.jobs.show', $emp([
                'job' => $job,
                'applicants' => $applicants,
                'criteria' => $criteria,
            ]));
        })->whereNumber('id')->name('employer.jobs.show');

        Route::post('/employer/jobs/{id}/close', function (Request $request, int $id) {
            $job = Job::where('company_id', $request->attributes->get('employerCompany')->id)->findOrFail($id);
            abort_unless($job->status === 'open', 409);
            $job->update(['status' => 'closed']);

            return redirect()->route('employer.jobs')->with('status', 'The job has been closed.');
        })->whereNumber('id')->name('employer.jobs.close');

        Route::post('/employer/jobs/{id}/duplicate', function (Request $request, int $id) {
            $job = Job::where('company_id', $request->attributes->get('employerCompany')->id)->findOrFail($id);
            $copy = $job->replicate();
            $copy->title = $job->title.' (copy)';
            $copy->slug = Str::slug($copy->title.'-'.$job->company->slug).'-'.Str::lower(Str::random(6));
            $copy->status = 'draft';
            $copy->moderated_by = null;
            $copy->moderated_at = null;
            $copy->moderation_note = null;
            $copy->save();

            return redirect()->route('employer.jobs')->with('status', 'A draft copy of the job has been created.');
        })->whereNumber('id')->name('employer.jobs.duplicate');
        Route::get('/employer/applicants/{id}', function (Request $request, int $id) use ($emp) {
            $application = JobApplication::with(['candidate.candidateProfile', 'job.company', 'cv', 'interviews'])
                ->whereKey($id)
                ->whereHas('job', fn ($query) => $query->where('company_id', $request->attributes->get('employerCompany')->id))
                ->firstOrFail();

            return view('employer.applicants.record', $emp([
                'application' => JobApplicationPresenter::forCompany($application),
            ]));
        })->whereNumber('id')->name('employer.applicants.show');
        Route::get('/employer/applicants/{id}/photo', [CandidateProfileController::class, 'photoForEmployer'])
            ->whereNumber('id')->name('employer.applicants.photo');
        Route::get('/employer/applicants/{id}/cv', [EmployerApplicationController::class, 'downloadCv'])
            ->whereNumber('id')->name('employer.applicants.cv');
        Route::post('/employer/applicants/{id}/status', [EmployerApplicationController::class, 'updateStatus'])
            ->whereNumber('id')->name('employer.applicants.status');
        Route::post('/employer/applicants/{id}/decline', [EmployerApplicationController::class, 'decline'])
            ->whereNumber('id')->name('employer.applicants.decline');
        Route::post('/employer/applicants/{id}/interviews', [EmployerApplicationController::class, 'scheduleInterview'])
            ->whereNumber('id')->name('employer.applicants.interviews.store');

        Route::get('/employer/interviews', function (Request $request) use ($emp, $companyInterviews) {
            $company = $request->attributes->get('employerCompany');

            return view('employer.interviews', $emp(['interviews' => $companyInterviews($company)]));
        })->name('employer.interviews');

    });
});

// ---- Admin area (admin role required in the real app) ----------------------
// Every admin view receives $admin, $notifications and $sidebarCounts.

Route::middleware(['auth:admin', 'role:admin'])->group(function () {
    $initials = static function (string $name): string {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return Str::upper(substr($parts[0] ?? '', 0, 1).(count($parts) > 1 ? substr($parts[count($parts) - 1], 0, 1) : ''));
    };
    $companySummary = static function (Company $company) use ($initials): array {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'initials' => $initials($company->name),
            'color' => '#1558d6',
            'logo_url' => $company->logo_path ? Storage::disk('public')->url($company->logo_path) : null,
            'industry' => $company->industry,
            'status' => ucfirst($company->status),
            'jobs' => $company->jobs_count ?? 0,
            'owner' => $company->contact_name ?: 'Not provided',
            'registered' => $company->created_at?->format('j M Y') ?? '',
            'checks' => [],
        ];
    };
    $adm = function (array $data = []) use ($initials): array {
        $user = request()->user();
        $pendingCount = Company::where('status', 'pending')->count();
        $openReportCount = Report::where('status', 'open')->count();

        return array_merge([
            'admin' => [
                'name' => $user->name,
                'first_name' => Str::before($user->name, ' '),
                'initials' => $initials($user->name),
                'role' => 'Administrator',
                'email' => $user->email,
            ],
            'notifications' => [],
            'sidebarCounts' => [
                'pending' => $pendingCount,
                'reports' => $openReportCount,
                'applications' => JobApplication::count(),
            ],
        ], $data);
    };

    Route::get('/admin', function (Request $request) use ($adm, $companySummary) {
        $range = (string) $request->query('range', '7');
        abort_unless(in_array($range, ['7', '30', '365'], true), 404);
        $since = $range === '365' ? now()->startOfYear() : now()->startOfDay()->subDays((int) $range - 1);
        $companies = Company::query()
            ->withCount(['jobs' => fn ($jobs) => $jobs->where('status', 'open')])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map($companySummary)
            ->all();
        $stats = [
            'users' => User::where('role', 'candidate')->count(),
            'companies' => Company::count(),
            'admins' => Admin::count(),
            'jobs' => Job::where('status', 'open')
                ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
                ->count(),
            'flagged_jobs' => Job::whereHas('reports', fn ($reports) => $reports->where('status', 'open'))->count(),
            'applications' => JobApplication::count(),
            'pending' => count($companies),
            'reports' => Report::where('status', 'open')->count(),
        ];
        $applicationCounts = JobApplication::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $applicationStages = collect([
            'submitted' => 'Submitted',
            'reviewing' => 'Reviewing',
            'shortlisted' => 'Shortlisted',
            'interview' => 'Interview',
            'offer' => 'Offer',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
        ])->mapWithKeys(fn (string $label, string $status) => [$label => $applicationCounts[$status] ?? 0])->all();
        $signups = [];
        if ($range === '7') {
            for ($offset = 6; $offset >= 0; $offset--) {
                $date = now()->subDays($offset);
                $signups[$date->format('D')] = User::where('role', 'candidate')->whereDate('created_at', $date)->count()
                    + Company::whereDate('created_at', $date)->count();
            }
        } elseif ($range === '30') {
            for ($offset = 5; $offset >= 0; $offset--) {
                $start = now()->startOfDay()->subDays($offset * 5 + 4);
                $end = now()->startOfDay()->subDays($offset * 5)->endOfDay();
                $signups[$start->format('j M')] = User::where('role', 'candidate')->whereBetween('created_at', [$start, $end])->count()
                    + Company::whereBetween('created_at', [$start, $end])->count();
            }
        } else {
            for ($offset = 11; $offset >= 0; $offset--) {
                $start = now()->startOfMonth()->subMonths($offset);
                $end = $start->copy()->endOfMonth();
                $signups[$start->format('M')] = User::where('role', 'candidate')->whereBetween('created_at', [$start, $end])->count()
                    + Company::whereBetween('created_at', [$start, $end])->count();
            }
        }
        $categories = Job::query()
            ->where('status', 'open')
            ->whereHas('company', fn ($companies) => $companies->where('status', 'verified'))
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'category')
            ->all();
        $activity = AdminActivityLog::with('admin')->latest()->limit(5)->get()->map(fn (AdminActivityLog $log) => [
            'icon' => str_starts_with($log->action, 'company.') ? 'building-check' : (str_starts_with($log->action, 'job.') ? 'briefcase' : (str_starts_with($log->action, 'report.') ? 'flag' : 'person-gear')),
            'tone' => str_starts_with($log->action, 'company.verified') || str_starts_with($log->action, 'job.restored') ? 'green' : 'orange',
            'text' => ($log->admin?->name ?? 'An administrator').' · '.Str::headline(str_replace('.', ' ', $log->action)).($log->details ? ' · '.$log->details : ''),
            'time' => $log->created_at?->diffForHumans() ?? '',
        ])->all();

        return view('admin.dashboard', $adm([
            'stats' => $stats,
            'applicationStages' => $applicationStages,
            'range' => $range,
            'rangeLabel' => ['7' => 'Last 7 days', '30' => 'Last 30 days', '365' => 'This year'][$range],
            'signups' => $signups,
            'companies' => $companies,
            'categories' => $categories,
            'activity' => $activity,
            'activityCount' => AdminActivityLog::where('created_at', '>=', $since)->count(),
        ]));
    })->name('admin.dashboard');

    Route::get('/admin/applications', function (Request $request) use ($adm) {
        $status = (string) $request->query('status', 'all');
        abort_unless(in_array($status, ['all', 'submitted', 'reviewing', 'shortlisted', 'interview', 'offer', 'rejected', 'withdrawn'], true), 404);
        $query = JobApplication::with(['candidate', 'job.company'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        $applications = $query->get()->map(fn (JobApplication $application) => [
            'id' => $application->id,
            'candidate_id' => $application->candidate->id,
            'candidate' => $application->candidate->name,
            'email' => $application->candidate->email,
            'job' => $application->job->title,
            'company' => $application->job->company->name,
            'status' => JobApplicationPresenter::statusLabel($application->status),
            'submitted' => $application->created_at?->format('j M Y, H:i') ?? '',
        ])->all();

        return view('admin.applications', $adm([
            'applications' => $applications,
            'status' => $status,
        ]));
    })->name('admin.applications');

    $usersQuery = static function (Request $request) {
        $role = (string) $request->query('role', 'all');
        $status = (string) $request->query('status', 'all');
        abort_unless(in_array($role, ['all', 'candidate'], true), 404);
        abort_unless(in_array($status, ['all', 'active', 'suspended'], true), 404);

        $query = User::query()->where('role', 'candidate')->withCount('applications')->latest();
        if ($role !== 'all') {
            $query->where('role', $role);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(fn ($users) => $users->where('name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%'));
        }

        return $query;
    };

    Route::get('/admin/users/export', function (Request $request) use ($usersQuery) {
        $users = $usersQuery($request)->get();

        return response()->streamDownload(function () use ($users): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Name', 'Email', 'Role', 'Status', 'Joined', 'Last login']);
            foreach ($users as $user) {
                fputcsv($output, [
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->status,
                    $user->created_at?->toDateTimeString(),
                    $user->last_login_at?->toDateTimeString(),
                ]);
            }
            fclose($output);
        }, 'employra-users-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    })->name('admin.users.export');

    Route::get('/admin/users', function (Request $request) use ($adm, $usersQuery) {
        $users = $usersQuery($request)->get()->map(fn (User $user) => [
            'id' => $user->id,
            'name' => $user->name,
            'initials' => JobPresenter::initials($user->name),
            'email' => $user->email,
            'role' => ucfirst($user->role),
            'status' => ucfirst($user->status),
            'joined' => $user->created_at?->format('j M Y') ?? '',
            'last_seen' => $user->last_login_at?->diffForHumans() ?? 'Never',
            'applications' => $user->applications_count,
        ]);

        return view('admin.users', $adm(['users' => $users]));
    })->name('admin.users');

    Route::get('/admin/users/{user}', function (User $user) use ($adm) {
        abort_unless($user->role === 'candidate', 404);
        $user->load([
            'candidateProfile.cvs' => fn ($cvs) => $cvs->latest(),
            'applications' => fn ($applications) => $applications->latest(),
            'applications.job.company',
            'applications.cv',
            'applications.statusHistory',
            'applications.interviews',
        ]);

        return view('admin.user-show', $adm(['user' => $user]));
    })->name('admin.users.show');

    Route::get('/admin/users/{user}/photo', [CandidateProfileController::class, 'photoForAdmin'])
        ->name('admin.users.photo');

    Route::get('/admin/users/{user}/cvs/{cv}', function (Request $request, User $user, int $cv) {
        abort_unless($user->role === 'candidate', 404);
        $profile = $user->candidateProfile()->firstOrFail();
        $document = $profile->cvs()->findOrFail($cv);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($document->stored_path), 404);

        AdminAudit::record(
            $request->user(),
            'user.cv_downloaded',
            'user',
            $user->id,
            'Candidate CV downloaded by an administrator.'
        );

        return $disk->download($document->stored_path, $document->original_name, [
            'Content-Type' => $document->mime,
        ]);
    })->whereNumber('cv')->name('admin.users.cvs.download');

    Route::post('/admin/users/{user}/suspend', function (Request $request, User $user) {
        abort_unless($user->role === 'candidate', 403);
        abort_unless($user->status === 'active', 409);
        DB::transaction(function () use ($request, $user): void {
            $user->forceFill(['status' => 'suspended'])->save();
            AdminAudit::record($request->user(), 'user.suspended', 'user', $user->id, $user->email);
        });

        return redirect()->route('admin.users')->with('status', $user->name.' has been suspended.');
    })->name('admin.users.suspend');

    Route::post('/admin/users/{user}/reactivate', function (Request $request, User $user) {
        abort_unless($user->role === 'candidate', 403);
        abort_unless($user->status === 'suspended', 409);
        DB::transaction(function () use ($request, $user): void {
            $user->forceFill(['status' => 'active'])->save();
            AdminAudit::record($request->user(), 'user.reactivated', 'user', $user->id, $user->email);
        });

        return redirect()->route('admin.users')->with('status', $user->name.' has been reactivated.');
    })->name('admin.users.reactivate');

    Route::post('/admin/users/{user}/reset-password', function (User $user) {
        abort_unless($user->role === 'candidate', 403);
        $status = Password::sendResetLink(['email' => $user->email]);
        if ($status !== Password::RESET_LINK_SENT) {
            return redirect()->route('admin.users')
                ->with('status', 'Password reset link could not be sent: '.trans($status))
                ->with('status_type', 'warning');
        }
        AdminAudit::record(request()->user(), 'user.password_reset_sent', 'user', $user->id, $user->email);

        return redirect()->route('admin.users')->with('status', 'A password reset link was sent to '.$user->email.'.');
    })->name('admin.users.reset-password');

    Route::post('/admin/users/{user}/message', function (Request $request, User $user) {
        abort_unless($user->role === 'candidate', 403);
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
        ]);
        Mail::raw($validated['message'], function ($mail) use ($user, $validated): void {
            $mail->to($user->email)->subject($validated['subject']);
        });
        AdminAudit::record($request->user(), 'user.message_sent', 'user', $user->id, $user->email);

        return redirect()->route('admin.users')->with('status', 'Your message was sent to '.$user->email.'.');
    })->name('admin.users.message');

    Route::get('/admin/companies', function (Request $r) use ($adm, $companySummary) {
        $status = $r->query('status', 'all');
        abort_unless(in_array($status, ['all', 'Pending', 'Verified', 'Rejected'], true), 404);
        $query = Company::query()
            ->withCount(['jobs' => fn ($jobs) => $jobs->where('status', 'open')])
            ->latest();
        if ($status !== 'all') {
            $query->where('status', Str::lower($status));
        }

        return view('admin.companies', $adm([
            'companies' => $query->get()->map($companySummary)->all(),
        ]));
    })->name('admin.companies');

    Route::get('/admin/companies/{company}', function (Company $company) use ($adm) {
        $jobIds = $company->jobs()->pluck('id');
        $jobs = $company->jobs()->withCount('applications')->latest()->paginate(10, ['*'], 'jobs_page');
        $applicationsCount = JobApplication::whereIn('job_id', $company->jobs()->select('id'))->count();
        $relatedReports = Report::query()
            ->where(function ($query) use ($company, $jobIds) {
                $query->where(function ($companyReports) use ($company) {
                    $companyReports->where('target_type', 'company')->where('target_id', $company->id);
                })->orWhere(function ($jobReports) use ($jobIds) {
                    $jobReports->where('target_type', 'job')->whereIn('target_id', $jobIds);
                });
            })
            ->latest();
        $reportCounts = [
            'total' => (clone $relatedReports)->count(),
            'open' => (clone $relatedReports)->where('status', 'open')->count(),
        ];
        $reports = (clone $relatedReports)->paginate(10, ['*'], 'reports_page');
        $reportIds = (clone $relatedReports)->pluck('id');
        $activities = AdminActivityLog::with('admin')
            ->where(function ($query) use ($company, $jobIds, $reportIds) {
                $query->where(function ($companyActivity) use ($company) {
                    $companyActivity->where('target_type', 'company')->where('target_id', $company->id);
                })->orWhere(function ($jobActivity) use ($jobIds) {
                    $jobActivity->where('target_type', 'job')->whereIn('target_id', $jobIds);
                })->orWhere(function ($reportActivity) use ($reportIds) {
                    $reportActivity->where('target_type', 'report')->whereIn('target_id', $reportIds);
                });
            })
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.company-show', $adm([
            'company' => $company,
            'logoUrl' => $company->logo_path ? Storage::disk('public')->url($company->logo_path) : null,
            'jobs' => $jobs,
            'jobCount' => $jobIds->count(),
            'openJobCount' => $company->jobs()->where('status', 'open')->count(),
            'applicationsCount' => $applicationsCount,
            'reportCounts' => $reportCounts,
            'reports' => $reports,
            'activities' => $activities,
        ]));
    })->name('admin.companies.show');

    Route::delete('/admin/companies/{company}', function (Request $request, Company $company) {
        $logoPath = $company->logo_path;

        DB::transaction(function () use ($request, $company): void {
            $jobIds = $company->jobs()->pluck('id');
            $relatedReports = Report::query()
                ->where(function ($query) use ($company, $jobIds) {
                    $query->where(function ($companyReports) use ($company) {
                        $companyReports->where('target_type', 'company')->where('target_id', $company->id);
                    })->orWhere(function ($jobReports) use ($jobIds) {
                        $jobReports->where('target_type', 'job')->whereIn('target_id', $jobIds);
                    });
                });
            $reportIds = (clone $relatedReports)->pluck('id');

            AdminActivityLog::query()
                ->where(function ($query) use ($company, $jobIds, $reportIds) {
                    $query->where(function ($companyActivity) use ($company) {
                        $companyActivity->where('target_type', 'company')->where('target_id', $company->id);
                    })->orWhere(function ($jobActivity) use ($jobIds) {
                        $jobActivity->where('target_type', 'job')->whereIn('target_id', $jobIds);
                    })->orWhere(function ($reportActivity) use ($reportIds) {
                        $reportActivity->where('target_type', 'report')->whereIn('target_id', $reportIds);
                    });
                })
                ->delete();

            $relatedReports->delete();
            $company->delete();

            AdminAudit::record(
                $request->user(),
                'company.permanently_deleted',
                null,
                null,
                'Company profile, jobs, applications, reports, and identifying audit entries were permanently deleted.'
            );
        });

        if ($logoPath !== null && Str::startsWith($logoPath, 'company-logos/')
            && Storage::disk('public')->exists($logoPath)
            && ! Storage::disk('public')->delete($logoPath)) {
            throw new \RuntimeException('Company data was deleted, but its logo file could not be removed.');
        }

        return redirect()->route('admin.companies')
            ->with('status', 'Company profile and related data were permanently deleted.')
            ->with('status_type', 'success');
    })->name('admin.companies.destroy');

    Route::post('/admin/companies/{company}/verify', function (Request $request, Company $company) {
        abort_unless($company->status === 'pending', 409);
        DB::transaction(function () use ($request, $company): void {
            $company->forceFill(['status' => 'verified'])->save();
            AdminAudit::record($request->user(), 'company.verified', 'company', $company->id, $company->name);
        });

        return redirect()->route('admin.companies')
            ->with('status', $company->name.' has been verified.');
    })->name('admin.companies.verify');

    Route::post('/admin/companies/{company}/reject', function (Request $request, Company $company) {
        abort_unless($company->status === 'pending', 409);
        DB::transaction(function () use ($request, $company): void {
            $company->forceFill(['status' => 'rejected'])->save();
            AdminAudit::record($request->user(), 'company.rejected', 'company', $company->id, $company->name);
        });

        return redirect()->route('admin.companies')
            ->with('status', $company->name.' has been rejected.');
    })->name('admin.companies.reject');

    Route::get('/admin/jobs', function (Request $request) use ($adm) {
        $status = (string) $request->query('status', 'all');
        abort_unless(in_array($status, ['all', 'Open', 'Flagged', 'Closed', 'Taken down', 'Draft'], true), 404);
        $query = Job::with('company')
            ->withCount('applications')
            ->withExists(['reports as has_open_reports' => fn ($reports) => $reports->where('status', 'open')])
            ->latest();
        if ($status === 'Flagged') {
            $query->whereHas('reports', fn ($reports) => $reports->where('status', 'open'));
        } elseif ($status === 'Closed') {
            $query->where('status', 'closed');
        } elseif ($status === 'Taken down') {
            $query->where('status', 'taken_down');
        } elseif ($status !== 'all') {
            $query->where('status', Str::lower($status));
        }
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(fn ($jobs) => $jobs->where('title', 'like', '%'.$search.'%')
                ->orWhereHas('company', fn ($companies) => $companies->where('name', 'like', '%'.$search.'%')));
        }

        return view('admin.jobs', $adm([
            'jobs' => $query->get()->map(fn (Job $job) => JobPresenter::forAdmin($job))->all(),
        ]));
    })->name('admin.jobs');

    Route::post('/admin/jobs/{job}/take-down', function (Request $request, Job $job) {
        abort_unless($job->status === 'open', 409);
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($request, $job, $validated): void {
            $job->forceFill([
                'status' => 'taken_down',
                'moderation_note' => trim($validated['reason'] ?? 'Removed by an administrator.'),
                'moderated_by' => $request->user()->id,
                'moderated_at' => now(),
            ])->save();
            AdminAudit::record($request->user(), 'job.taken_down', 'job', $job->id, $job->title);
        });

        return redirect()->route('admin.jobs')->with('status', 'The job was taken down.');
    })->name('admin.jobs.take-down');

    Route::get('/admin/jobs/{job}', function (Job $job) use ($adm) {
        $job->load('company')->loadCount('applications');

        return view('admin.job-show', $adm(['job' => $job]));
    })->name('admin.jobs.show');

    Route::post('/admin/jobs/{job}/keep-live', function (Request $request, Job $job) {
        abort_unless(in_array($job->status, ['open', 'taken_down'], true), 409);
        DB::transaction(function () use ($request, $job): void {
            $job->forceFill([
                'status' => 'open',
                'moderation_note' => null,
                'moderated_by' => $request->user()->id,
                'moderated_at' => now(),
            ])->save();
            AdminAudit::record($request->user(), 'job.restored', 'job', $job->id, $job->title);
        });

        return redirect()->route('admin.jobs')->with('status', 'The job is live.');
    })->name('admin.jobs.keep-live');
    Route::get('/admin/reports', function (Request $request) use ($adm) {
        $status = (string) $request->query('status', 'all');
        abort_unless(in_array($status, ['all', 'open', 'actioned', 'dismissed'], true), 404);
        $query = Report::latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->get()->map(fn (Report $report) => [
            'id' => $report->id,
            'type' => ucfirst($report->target_type),
            'target' => $report->target_label,
            'reason' => $report->reason,
            'by' => $report->reporter_name ?? 'Anonymous',
            'reporter_email' => $report->reporter_email,
            'when' => $report->created_at?->diffForHumans() ?? '',
            'status' => ucfirst($report->status),
            'severity' => ucfirst($report->severity),
            'detail' => $report->details,
        ])->all();

        return view('admin.reports', $adm(['reports' => $reports]));
    })->name('admin.reports');

    Route::post('/admin/reports/{report}/action', function (Request $request, Report $report) {
        abort_unless($report->status === 'open', 409);
        $validated = $request->validate(['resolution_note' => ['nullable', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $report, $validated): void {
            if ($report->target_type === 'job') {
                $job = Job::lockForUpdate()->findOrFail($report->target_id);
                $job->forceFill([
                    'status' => 'taken_down',
                    'moderation_note' => $validated['resolution_note'] ?? 'Removed after review of a user report.',
                    'moderated_by' => $request->user()->id,
                    'moderated_at' => now(),
                ])->save();
            } elseif ($report->target_type === 'company') {
                Company::lockForUpdate()->findOrFail($report->target_id)
                    ->forceFill(['status' => 'rejected'])
                    ->save();
            } elseif ($report->target_type === 'user') {
                $target = \App\Models\User::lockForUpdate()->findOrFail($report->target_id);
                abort_unless($target->role === 'candidate', 422);
                $target->forceFill(['status' => 'suspended'])->save();
            } else {
                abort(422, 'This report target cannot be moderated.');
            }

            $report->forceFill([
                'status' => 'actioned',
                'resolution_note' => $validated['resolution_note'] ?? 'Moderation applied to the reported item.',
                'resolved_by' => $request->user()->id,
                'resolved_at' => now(),
            ])->save();
            AdminAudit::record($request->user(), 'report.actioned', 'report', $report->id, $report->target_label);
        });

        return redirect()->route('admin.reports')->with('status', 'Report actioned and the reported item was moderated.');
    })->name('admin.reports.action');

    Route::post('/admin/reports/{report}/dismiss', function (Request $request, Report $report) {
        abort_unless($report->status === 'open', 409);
        DB::transaction(function () use ($request, $report): void {
            $report->forceFill([
                'status' => 'dismissed',
                'resolution_note' => 'Dismissed by an administrator.',
                'resolved_by' => $request->user()->id,
                'resolved_at' => now(),
            ])->save();
            AdminAudit::record($request->user(), 'report.dismissed', 'report', $report->id, $report->target_label);
        });

        return redirect()->route('admin.reports')->with('status', 'Report dismissed.');
    })->name('admin.reports.dismiss');

    Route::post('/admin/reports/{report}/contact', function (Request $request, Report $report) {
        abort_unless($report->status === 'open', 409);
        abort_if(blank($report->reporter_email), 422, 'This report has no contact email.');
        $validated = $request->validate(['message' => ['required', 'string', 'min:5', 'max:3000']]);

        Mail::raw($validated['message'], function ($mail) use ($report): void {
            $mail->to($report->reporter_email)->subject('Update on your Employra report');
        });
        AdminAudit::record($request->user(), 'report.contacted', 'report', $report->id, $report->reporter_email);

        return redirect()->route('admin.reports')->with('status', 'A message was sent to the reporter.');
    })->name('admin.reports.contact');
    $auditQuery = static function (Request $request) {
        $type = (string) $request->query('type', 'all');
        $range = (string) $request->query('range', '30');
        $prefixes = [
            'companies' => 'company.',
            'jobs' => 'job.',
            'reports' => 'report.',
            'users' => 'user.',
            'settings' => 'settings.',
            'admins' => 'admin.',
        ];
        abort_unless($type === 'all' || array_key_exists($type, $prefixes), 404);
        abort_unless(in_array($range, ['7', '30', '365'], true), 404);

        $query = AdminActivityLog::with('admin')->where('created_at', '>=', now()->subDays((int) $range));
        if ($type !== 'all') {
            $query->where('action', 'like', $prefixes[$type].'%');
        }

        return $query->latest();
    };

    Route::get('/admin/ai-log/export', function (Request $request) use ($auditQuery) {
        $logs = $auditQuery($request)->get();

        return response()->streamDownload(function () use ($logs): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['When', 'Admin', 'Action', 'Target type', 'Target id', 'Details']);
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log->created_at?->toDateTimeString(),
                    $log->admin?->name ?? 'Deleted admin',
                    $log->action,
                    $log->target_type,
                    $log->target_id,
                    $log->details,
                ]);
            }
            fclose($output);
        }, 'employra-admin-activity-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    })->name('admin.audit.export');

    Route::get('/admin/ai-log', function (Request $request) use ($adm, $auditQuery) {
        $logs = $auditQuery($request)->get();
        $reportActions = AdminActivityLog::where('action', 'like', 'report.%')->where('created_at', '>=', now()->subDays(30));

        return view('admin.audit', $adm([
            'audit' => $logs->map(fn (AdminActivityLog $log) => [
                'when' => $log->created_at?->format('j M Y, H:i') ?? '',
                'who' => $log->admin?->name ?? 'Deleted admin',
                'action' => Str::headline(str_replace('.', ' ', $log->action)),
                'target' => $log->target_type ? ucfirst($log->target_type).' #'.$log->target_id : '',
                'details' => $log->details ?? '',
            ])->all(),
            'auditStats' => [
                'actions' => AdminActivityLog::count(),
                'week' => AdminActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
                'reports' => (clone $reportActions)->where('action', 'report.actioned')->count(),
                'jobs' => AdminActivityLog::where('action', 'job.taken_down')->count(),
            ],
        ]));
    })->name('admin.audit');
    Route::get('/admin/settings', function () use ($adm) {
        $settings = [
            'platform_name' => PlatformSettings::get('platform_name', 'Employra'),
            'support_email' => PlatformSettings::get('support_email', 'support@employra.example'),
            'job_categories' => PlatformSettings::get('job_categories', MockData::categories()),
            'allow_registration' => PlatformSettings::get('allow_registration', true),
        ];

        return view('admin.settings', $adm([
            'settings' => $settings,
            'admins' => Admin::orderBy('name')->get(),
        ]));
    })->name('admin.settings');

    Route::post('/admin/settings', function (Request $request) {
        $validated = $request->validate([
            'platform_name' => ['required', 'string', 'max:120'],
            'support_email' => ['required', 'email', 'max:255'],
            'job_categories' => ['required', 'string', 'max:5000'],
        ]);
        $categories = array_values(array_unique(array_filter(array_map(
            static fn (string $category): string => trim($category),
            preg_split('/\R/', $validated['job_categories']) ?: []
        ), static fn (string $category): bool => $category !== '')));
        abort_if($categories === [] || count($categories) > 50, 422, 'Enter between 1 and 50 job categories.');

        DB::transaction(function () use ($validated, $categories, $request): void {
            PlatformSettings::set('platform_name', $validated['platform_name'], $request->user()->id);
            PlatformSettings::set('support_email', $validated['support_email'], $request->user()->id);
            PlatformSettings::set('job_categories', $categories, $request->user()->id);
            PlatformSettings::set('allow_registration', $request->boolean('allow_registration'), $request->user()->id);
            AdminAudit::record($request->user(), 'settings.updated', 'settings', null, 'Updated platform settings.');
        });

        return redirect()->route('admin.settings')->with('status', 'Platform settings have been saved.');
    })->name('admin.settings.update');

    Route::get('/admin/admins/create', fn () => view('admin.admin-create', $adm()))->name('admin.admins.create');
    Route::post('/admin/admins', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email'),
                Rule::unique('companies', 'email'),
                Rule::unique('admins', 'email'),
            ],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        DB::transaction(function () use ($validated, $request): void {
            $administrator = Admin::create([
                'name' => $validated['name'],
                'email' => Str::lower($validated['email']),
                'password' => $validated['password'],
                'status' => 'active',
            ]);
            AdminAudit::record($request->user(), 'admin.created', 'admin', $administrator->id, $administrator->email);
        });

        return redirect()->route('admin.settings')->with('status', 'Administrator account created.');
    })->name('admin.admins.store');
});

// ---- Start page listing every screen --------------------------------------

Route::get('/gui-preview', function () {
    $job = MockData::jobs()[0];
    $company = MockData::companies()[0];

    return view('preview.index', ['sampleJob' => $job, 'sampleCompany' => $company]);
})->name('preview.index');
