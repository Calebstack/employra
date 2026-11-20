<?php

namespace App\Support;

/**
 * TEMPORARY sample data for the candidate area of the Employra GUI preview.
 * Delete together with routes/gui-preview.php once the real backend exists.
 * Views read plain arrays shaped like these; return the same keys from your controllers.
 */
class CandidateMockData
{
    /** Pipeline stages in order. "Closed" is a terminal state shown separately. */
    public static function stages(): array
    {
        return ['Submitted', 'Reviewing', 'Shortlisted', 'Interview', 'Offer'];
    }

    public static function candidate(): array
    {
        return [
            'name' => 'Adaeze Okafor',
            'initials' => 'AO',
            'first_name' => 'Adaeze',
            'last_name' => 'Okafor',
            'email' => 'adaeze.okafor@example.com',
            'phone' => '0801 234 5678',
            'headline' => 'Junior PHP / Laravel Developer',
            'location' => 'Lagos, Nigeria',
            'summary' => 'Computer Science graduate who builds web applications with PHP, Laravel and MySQL. Completed an NYSC year at a software company and have shipped two portfolio projects with real users.',
            'skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Bootstrap', 'Git', 'REST APIs', 'AJAX'],
            'experience' => [
                ['title' => 'Junior Web Developer (NYSC)', 'company' => 'Powerline Software Ltd', 'period' => 'Nov 2024 – Oct 2025', 'location' => 'Lagos', 'description' => 'Built and maintained internal dashboards in Laravel, fixed bugs reported by staff and wrote basic API endpoints used by a mobile app.'],
                ['title' => 'Freelance Web Developer', 'company' => 'Self-employed', 'period' => 'Jan 2023 – Present', 'location' => 'Remote', 'description' => 'Designed and built small business websites for local clients using HTML, Bootstrap and PHP.'],
            ],
            'education' => [
                ['degree' => 'B.Sc. Computer Science', 'school' => 'Redeemer’s University', 'period' => '2020 – 2024', 'grade' => 'Second Class Upper'],
                ['degree' => 'Software Development Certificate', 'school' => 'Moat Academy', 'period' => '2023', 'grade' => ''],
            ],
            'preferred_roles' => 'Backend Developer, Laravel Developer, Full-stack Developer',
            'preferred_locations' => 'Lagos, Remote',
            'expected_salary' => '₦400k – ₦600k / month',
            'availability' => 'Available immediately',
            'visibility' => 'employers_i_apply_to',
            'completion' => 85,
            'completion_missing' => ['Add a profile photo', 'Add a LinkedIn or portfolio link'],
        ];
    }

    public static function notifications(): array
    {
        return [
            ['icon' => 'calendar-check', 'tone' => 'orange', 'text' => 'Interview scheduled with BrightPath Technologies', 'time' => '2 hours ago'],
            ['icon' => 'eye', 'tone' => 'blue', 'text' => 'NovaPay is reviewing your application', 'time' => 'Yesterday'],
            ['icon' => 'stars', 'tone' => 'green', 'text' => 'Your CV analysis is ready', 'time' => '2 days ago'],
        ];
    }

    public static function cvs(): array
    {
        return [
            ['id' => 1, 'filename' => 'Adaeze_Okafor_CV_2026.pdf', 'type' => 'PDF', 'size' => '184 KB', 'uploaded' => '5 Nov 2026', 'status' => 'Analyzed', 'primary' => true, 'used_in' => 3],
            ['id' => 2, 'filename' => 'Adaeze_Okafor_Data_Analyst_CV.docx', 'type' => 'DOCX', 'size' => '96 KB', 'uploaded' => 'Today', 'status' => 'Processing', 'primary' => false, 'used_in' => 0],
            ['id' => 3, 'filename' => 'old-cv-scan.pdf', 'type' => 'PDF', 'size' => '2.4 MB', 'uploaded' => '12 Sep 2026', 'status' => 'Failed', 'primary' => false, 'used_in' => 0],
        ];
    }

    public static function cv(int $id): ?array
    {
        foreach (self::cvs() as $cv) {
            if ($cv['id'] === $id) {
                return $cv;
            }
        }

        return null;
    }

    /** Explainable CV analysis for a CV. */
    public static function analysis(int $cvId = 1): array
    {
        return [
            'cv_id' => $cvId,
            'analyzed_at' => '5 Nov 2026, 3:42 PM',
            'readiness' => 82,
            'summary' => 'Your CV is clear and easy to scan, and your technical skills are well covered. The biggest opportunity is to show the impact of your work with numbers and to tighten your summary.',
            'ratings' => [
                ['label' => 'Structure', 'level' => 'Good', 'tone' => 'green', 'note' => 'Sections are in a sensible order with clear headings.'],
                ['label' => 'Clarity', 'level' => 'Good', 'tone' => 'green', 'note' => 'Sentences are short and easy to follow.'],
                ['label' => 'Achievements', 'level' => 'Needs work', 'tone' => 'orange', 'note' => 'Few results are backed by numbers.'],
                ['label' => 'Keywords', 'level' => 'Fair', 'tone' => 'blue', 'note' => 'Some common role keywords are missing.'],
            ],
            'extracted' => [
                'skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Bootstrap', 'Git', 'REST APIs', 'AJAX'],
                'experience' => ['Junior Web Developer (NYSC) · Powerline Software Ltd · Nov 2024 – Oct 2025', 'Freelance Web Developer · Self-employed · Jan 2023 – Present'],
                'education' => ['B.Sc. Computer Science · Redeemer’s University · 2024', 'Software Development Certificate · Moat Academy · 2023'],
            ],
            'strengths' => [
                ['title' => 'Clear section headings', 'detail' => 'Recruiters can find your skills and experience in seconds.', 'section' => 'Structure'],
                ['title' => 'Relevant technical skills', 'detail' => 'PHP, Laravel and MySQL appear in both your skills list and experience.', 'section' => 'Skills'],
                ['title' => 'Education is easy to verify', 'detail' => 'Degree, school and years are all stated.', 'section' => 'Education'],
            ],
            'improvements' => [
                ['priority' => 'High', 'title' => 'Add numbers to your achievements', 'detail' => 'Instead of “built dashboards”, say how many users used them or how much time they saved.', 'example' => 'Built 4 internal dashboards used by 30+ staff, cutting weekly reporting time by 5 hours.', 'section' => 'Experience'],
                ['priority' => 'Medium', 'title' => 'Shorten your summary to 2–3 lines', 'detail' => 'Focus on the kind of role you want and your strongest skills.', 'example' => 'Laravel developer with 2 years of project experience building APIs and dashboards. Looking for a backend role.', 'section' => 'Summary'],
                ['priority' => 'Medium', 'title' => 'List projects with links', 'detail' => 'Add links to your portfolio or GitHub next to each project so employers can see real work.', 'example' => '', 'section' => 'Projects'],
                ['priority' => 'Low', 'title' => 'Use one date format', 'detail' => 'You mix “Nov 2024” and “11/2024”. Pick one.', 'example' => '', 'section' => 'Formatting'],
            ],
            'compare' => [
                'job_slug' => 'php-laravel-developer-brightpath',
                'job_title' => 'PHP / Laravel Developer',
                'company' => 'BrightPath Technologies',
                'matched' => [
                    ['skill' => 'PHP', 'evidence' => 'Skills list and NYSC experience', 'section' => 'Experience'],
                    ['skill' => 'Laravel', 'evidence' => 'Built dashboards in Laravel', 'section' => 'Experience'],
                    ['skill' => 'MySQL', 'evidence' => 'Listed in skills', 'section' => 'Skills'],
                    ['skill' => 'REST APIs', 'evidence' => 'Wrote API endpoints for a mobile app', 'section' => 'Experience'],
                    ['skill' => 'Git', 'evidence' => 'Listed in skills', 'section' => 'Skills'],
                ],
                'partial' => [
                    ['skill' => 'Years of experience', 'evidence' => 'Role asks for 2+ years of professional work; your CV shows about 1 year of employment plus freelance work', 'section' => 'Experience'],
                ],
                'missing' => ['Docker', 'CI/CD', 'AWS', 'Queues & scheduled jobs'],
            ],
        ];
    }

    public static function applications(): array
    {
        $jobs = [];
        foreach (MockData::jobs() as $j) {
            $jobs[$j['slug']] = $j;
        }

        $rows = [
            ['id' => 101, 'job_slug' => 'php-laravel-developer-brightpath', 'status' => 'Interview', 'submitted' => '8 Nov 2026', 'updated' => '2 hours ago', 'cv' => 'Adaeze_Okafor_CV_2026.pdf', 'match' => 88,
                'timeline' => [
                    ['stage' => 'Submitted', 'date' => '8 Nov 2026', 'note' => 'Your application was received.'],
                    ['stage' => 'Reviewing', 'date' => '9 Nov 2026', 'note' => 'The hiring team started reviewing your profile.'],
                    ['stage' => 'Shortlisted', 'date' => '11 Nov 2026', 'note' => 'You were shortlisted for an interview.'],
                    ['stage' => 'Interview', 'date' => '12 Nov 2026', 'note' => 'Interview scheduled. See details below.'],
                    ['stage' => 'Offer', 'date' => null, 'note' => ''],
                ],
                'interview' => ['date' => 'Tuesday, 17 Nov 2026', 'time' => '10:00 AM (WAT)', 'mode' => 'Video call', 'where' => 'Link will be sent by email 1 hour before', 'with' => 'Tunde Bakare, Engineering Lead', 'notes' => 'Expect a short technical conversation about Laravel and a walk-through of one of your projects.']],
            ['id' => 102, 'job_slug' => 'junior-data-analyst-novapay', 'status' => 'Reviewing', 'submitted' => '10 Nov 2026', 'updated' => 'Yesterday', 'cv' => 'Adaeze_Okafor_CV_2026.pdf', 'match' => 61,
                'timeline' => [
                    ['stage' => 'Submitted', 'date' => '10 Nov 2026', 'note' => 'Your application was received.'],
                    ['stage' => 'Reviewing', 'date' => '11 Nov 2026', 'note' => 'The hiring team is reviewing your profile.'],
                    ['stage' => 'Shortlisted', 'date' => null, 'note' => ''],
                    ['stage' => 'Interview', 'date' => null, 'note' => ''],
                    ['stage' => 'Offer', 'date' => null, 'note' => ''],
                ], 'interview' => null],
            ['id' => 103, 'job_slug' => 'frontend-intern-brightpath', 'status' => 'Shortlisted', 'submitted' => '3 Nov 2026', 'updated' => '3 days ago', 'cv' => 'Adaeze_Okafor_CV_2026.pdf', 'match' => 79,
                'timeline' => [
                    ['stage' => 'Submitted', 'date' => '3 Nov 2026', 'note' => 'Your application was received.'],
                    ['stage' => 'Reviewing', 'date' => '5 Nov 2026', 'note' => 'The hiring team started reviewing your profile.'],
                    ['stage' => 'Shortlisted', 'date' => '9 Nov 2026', 'note' => 'You were shortlisted. Expect an interview invitation soon.'],
                    ['stage' => 'Interview', 'date' => null, 'note' => ''],
                    ['stage' => 'Offer', 'date' => null, 'note' => ''],
                ], 'interview' => null],
            ['id' => 104, 'job_slug' => 'digital-marketing-executive-greenfield', 'status' => 'Submitted', 'submitted' => 'Today', 'updated' => 'Just now', 'cv' => 'Adaeze_Okafor_CV_2026.pdf', 'match' => 42,
                'timeline' => [
                    ['stage' => 'Submitted', 'date' => 'Today', 'note' => 'Your application was received.'],
                    ['stage' => 'Reviewing', 'date' => null, 'note' => ''],
                    ['stage' => 'Shortlisted', 'date' => null, 'note' => ''],
                    ['stage' => 'Interview', 'date' => null, 'note' => ''],
                    ['stage' => 'Offer', 'date' => null, 'note' => ''],
                ], 'interview' => null],
            ['id' => 105, 'job_slug' => 'loan-officer-harvest', 'status' => 'Closed', 'submitted' => '20 Oct 2026', 'updated' => '1 week ago', 'cv' => 'Adaeze_Okafor_CV_2026.pdf', 'match' => 35,
                'timeline' => [
                    ['stage' => 'Submitted', 'date' => '20 Oct 2026', 'note' => 'Your application was received.'],
                    ['stage' => 'Reviewing', 'date' => '24 Oct 2026', 'note' => 'The hiring team reviewed your profile.'],
                    ['stage' => 'Shortlisted', 'date' => null, 'note' => ''],
                    ['stage' => 'Interview', 'date' => null, 'note' => ''],
                    ['stage' => 'Offer', 'date' => null, 'note' => ''],
                ], 'interview' => null, 'closed_note' => 'The employer decided to move forward with other candidates. Thank you for applying.'],
        ];

        return array_map(function ($a) use ($jobs) {
            $job = $jobs[$a['job_slug']];
            $a['job_title'] = $job['title'];
            $a['company'] = $job['company'];
            $a['company_initials'] = $job['company_initials'];
            $a['company_color'] = $job['company_color'];
            $a['location'] = $job['location'];
            $a['mode'] = $job['mode'];
            $a['type'] = $job['type'];
            $a['salary'] = $job['salary'];

            return $a;
        }, $rows);
    }

    public static function application(int $id): ?array
    {
        foreach (self::applications() as $a) {
            if ($a['id'] === $id) {
                return $a;
            }
        }

        return null;
    }

    /** Jobs recommended to the candidate, each with an explainable match. */
    public static function recommendations(): array
    {
        $reasons = [
            'php-laravel-developer-brightpath' => [88, ['PHP', 'Laravel', 'MySQL', 'REST APIs'], ['Docker']],
            'frontend-intern-brightpath' => [79, ['JavaScript', 'Bootstrap'], ['HTML/CSS portfolio link']],
            'junior-data-analyst-novapay' => [61, ['SQL basics (MySQL)', 'Python interest'], ['Power BI', 'Excel']],
            'ui-ux-designer-pixelcraft' => [48, ['Design experience'], ['Figma', 'UX Research']],
            'customer-support-agent-novapay' => [44, ['Communication'], ['Zendesk']],
        ];
        $out = [];
        foreach ($reasons as $slug => [$match, $have, $gaps]) {
            $job = MockData::job($slug);
            $job['match'] = $match;
            $job['match_have'] = $have;
            $job['match_gaps'] = $gaps;
            $out[] = $job;
        }

        return $out;
    }

    public static function savedJobs(): array
    {
        return array_map(function ($slug, $savedOn) {
            $j = MockData::job($slug);
            $j['saved_on'] = $savedOn;

            return $j;
        }, ['ui-ux-designer-pixelcraft', 'clinic-operations-manager-lumina', 'customer-support-agent-novapay', 'fleet-coordinator-summit'], ['Saved 2 days ago', 'Saved 4 days ago', 'Saved today', 'Saved last week']);
    }

    /** Tips shown on the dashboard. */
    public static function tips(): array
    {
        return [
            ['icon' => 'graph-up-arrow', 'text' => 'Add numbers to your achievements. CVs with measurable results get read more closely.'],
            ['icon' => 'link-45deg', 'text' => 'Link your GitHub or portfolio so employers can see real work.'],
            ['icon' => 'bookmark-star', 'text' => 'Save jobs you like and compare them against your CV before applying.'],
        ];
    }
}
