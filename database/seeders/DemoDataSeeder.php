<?php

namespace Database\Seeders;

use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo accounts can only be seeded in local or testing environments.');
        }

        DB::transaction(function (): void {
            $companyEmail = 'demo-company@employra.test';
            $candidateEmail = 'demo-candidate@employra.test';
            $companyPassword = Company::where('email', $companyEmail)->exists() ? null : Str::password(24);
            $candidatePassword = User::where('email', $candidateEmail)->exists() ? null : Str::password(24);

            $company = Company::firstOrCreate(
                ['slug' => 'employra-demo-company'],
                [
                    'name' => 'Employra Demo Company',
                    'industry' => 'Software & IT',
                    'status' => 'verified',
                    'contact_name' => 'Demo Company Contact',
                    'email' => $companyEmail,
                    'password' => $companyPassword,
                    'account_status' => 'active',
                    'website' => 'https://employra-demo.example',
                    'location' => 'Lagos, Nigeria',
                    'company_size' => '11–50 employees',
                    'description' => 'A sample company profile for testing company pages, employer tools, and admin review.',
                    'registration_number' => 'DEMO-REG-001',
                ]
            );
            $candidate = User::firstOrCreate(
                ['email' => $candidateEmail],
                [
                    'name' => 'Demo Candidate',
                    'password' => $candidatePassword,
                    'role' => 'candidate',
                    'status' => 'active',
                ]
            );
            CandidateProfile::firstOrCreate(
                ['user_id' => $candidate->id],
                ['onboarding_completed_at' => now(), 'completion' => 100]
            );

            $jobs = [
                [
                    'title' => 'Demo Laravel Developer',
                    'slug' => 'demo-laravel-developer',
                    'category' => 'Engineering',
                    'experience_level' => 'Mid level',
                    'job_type' => 'Full-time',
                    'work_mode' => 'Hybrid',
                    'location' => 'Lagos, Nigeria',
                    'salary_min' => 450000,
                    'salary_max' => 700000,
                    'summary' => 'Build and maintain Laravel applications with a collaborative product team.',
                    'responsibilities' => "Build Laravel features\nWrite and review API code\nSupport production releases",
                    'benefits' => "Health insurance\nLearning budget\nFlexible hybrid schedule",
                    'required_skills' => ['PHP', 'Laravel', 'MySQL'],
                    'preferred_skills' => ['Docker', 'Queues'],
                ],
                [
                    'title' => 'Demo Product Designer',
                    'slug' => 'demo-product-designer',
                    'category' => 'Design',
                    'experience_level' => 'Mid level',
                    'job_type' => 'Full-time',
                    'work_mode' => 'Remote',
                    'location' => 'Remote (Nigeria)',
                    'salary_min' => 350000,
                    'salary_max' => 550000,
                    'summary' => 'Design clear, accessible product experiences from early research through hand-off.',
                    'responsibilities' => "Create wireframes and prototypes\nMaintain design systems\nCollaborate with engineers",
                    'benefits' => "Remote work\nFlexible hours\nPaid time off",
                    'required_skills' => ['Figma', 'UX Research', 'Prototyping'],
                    'preferred_skills' => ['Design Systems', 'Accessibility'],
                ],
                [
                    'title' => 'Demo Data Analyst',
                    'slug' => 'demo-data-analyst',
                    'category' => 'Data & AI',
                    'experience_level' => 'Entry level',
                    'job_type' => 'Full-time',
                    'work_mode' => 'Hybrid',
                    'location' => 'Lagos, Nigeria',
                    'salary_min' => 250000,
                    'salary_max' => 400000,
                    'summary' => 'Turn business data into reliable reports and practical product insights.',
                    'responsibilities' => "Build dashboards\nWrite SQL queries\nValidate and document metrics",
                    'benefits' => "Mentorship\nHealth insurance\nLearning budget",
                    'required_skills' => ['SQL', 'Excel', 'Power BI'],
                    'preferred_skills' => ['Python', 'Metabase'],
                ],
            ];

            foreach ($jobs as $job) {
                Job::updateOrCreate(
                    ['slug' => $job['slug']],
                    array_merge($job, [
                        'company_id' => $company->id,
                        'deadline' => now()->addDays(30)->toDateString(),
                        'min_experience_years' => 1,
                        'status' => 'open',
                    ])
                );
            }

            $firstJob = Job::where('slug', 'demo-laravel-developer')->firstOrFail();
            JobApplication::firstOrCreate(
                ['user_id' => $candidate->id, 'job_id' => $firstJob->id],
                [
                    'status' => 'submitted',
                    'cover_note' => 'This is a demo application for testing the candidate, company, and admin application views.',
                ]
            );

            if ($companyPassword !== null) {
                $this->command?->info('Demo company sign-in: '.$companyEmail.' / '.$companyPassword);
            }
            if ($candidatePassword !== null) {
                $this->command?->info('Demo candidate sign-in: '.$candidateEmail.' / '.$candidatePassword);
            }
        });
    }
}
