<?php

namespace App\Support;

/**
 * TEMPORARY sample data for the employer area of the Employra GUI preview.
 * Delete together with routes/gui-preview.php once the real backend exists.
 * The employer shown is BrightPath Technologies.
 */
class EmployerMockData
{
    public static function stages(): array
    {
        return ['New', 'Reviewing', 'Shortlisted', 'Interview', 'Offer'];
    }

    public static function employer(): array
    {
        return ['name' => 'Tunde Bakare', 'first_name' => 'Tunde', 'initials' => 'TB', 'role' => 'Engineering Lead', 'email' => 'tunde@brightpath.example', 'company' => MockData::company('brightpath-tech')];
    }

    public static function notifications(): array
    {
        return [
            ['icon' => 'person-plus', 'tone' => 'blue', 'text' => '3 new applicants for PHP / Laravel Developer', 'time' => '1 hour ago'],
            ['icon' => 'calendar-check', 'tone' => 'orange', 'text' => 'Adaeze Okafor confirmed her interview', 'time' => 'Today'],
            ['icon' => 'clock-history', 'tone' => 'green', 'text' => 'Frontend Developer Intern closes in 18 days', 'time' => 'Yesterday'],
        ];
    }

    public static function jobs(): array
    {
        return [
            ['id' => 1, 'slug' => 'php-laravel-developer-brightpath', 'title' => 'PHP / Laravel Developer', 'status' => 'Open', 'location' => 'Lagos, Nigeria', 'mode' => 'Hybrid', 'type' => 'Full-time', 'posted' => '2 days ago', 'deadline' => '30 Nov 2026', 'applicants' => 6, 'new' => 3, 'views' => 214],
            ['id' => 2, 'slug' => 'frontend-intern-brightpath', 'title' => 'Frontend Developer Intern', 'status' => 'Open', 'location' => 'Lagos, Nigeria', 'mode' => 'Hybrid', 'type' => 'Internship', 'posted' => '2 days ago', 'deadline' => '01 Dec 2026', 'applicants' => 14, 'new' => 5, 'views' => 389],
            ['id' => 3, 'slug' => null, 'title' => 'DevOps Engineer', 'status' => 'Draft', 'location' => 'Remote', 'mode' => 'Remote', 'type' => 'Full-time', 'posted' => 'Not published', 'deadline' => '—', 'applicants' => 0, 'new' => 0, 'views' => 0],
            ['id' => 4, 'slug' => null, 'title' => 'QA Tester', 'status' => 'Closed', 'location' => 'Lagos, Nigeria', 'mode' => 'On-site', 'type' => 'Contract', 'posted' => '2 months ago', 'deadline' => '15 Sep 2026', 'applicants' => 22, 'new' => 0, 'views' => 540],
        ];
    }

    public static function job(int $id): ?array
    {
        foreach (self::jobs() as $j) {
            if ($j['id'] === $id) {
                return $j;
            }
        }

        return null;
    }

    /** Criteria the employer can weight when reviewing (they control the weights). */
    public static function criteria(): array
    {
        return [
            ['key' => 'skills', 'label' => 'Required skills', 'weight' => 40],
            ['key' => 'experience', 'label' => 'Relevant experience', 'weight' => 30],
            ['key' => 'education', 'label' => 'Education & training', 'weight' => 15],
            ['key' => 'keywords', 'label' => 'Role keywords', 'weight' => 15],
        ];
    }

    private static function applicantRows(): array
    {
        return [
            ['id' => 11, 'name' => 'Adaeze Okafor', 'headline' => 'Junior PHP / Laravel Developer', 'location' => 'Lagos', 'years' => 1.5, 'score' => 88, 'status' => 'Interview', 'applied' => '8 Nov 2026',
                'breakdown' => ['skills' => 92, 'experience' => 78, 'education' => 90, 'keywords' => 88],
                'matched' => [['PHP', 'Listed in skills and used daily in NYSC role', 'Experience'], ['Laravel', 'Built internal dashboards in Laravel', 'Experience'], ['MySQL', 'Listed in skills', 'Skills'], ['REST APIs', 'Wrote API endpoints for a mobile app', 'Experience']],
                'partial' => [['Years of experience', 'About 1.5 years including freelance; role asks for 2+', 'Experience']], 'missing' => ['Docker', 'CI/CD'],
                'skills' => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Bootstrap', 'Git', 'REST APIs'],
                'summary' => 'Strong match on required skills, backed by hands-on Laravel work. Slightly under the experience asked for.'],
            ['id' => 12, 'name' => 'Emeka Nwosu', 'headline' => 'Backend Developer', 'location' => 'Abuja', 'years' => 4, 'score' => 84, 'status' => 'Shortlisted', 'applied' => '9 Nov 2026',
                'breakdown' => ['skills' => 85, 'experience' => 92, 'education' => 70, 'keywords' => 82],
                'matched' => [['PHP', '4 years of PHP across two employers', 'Experience'], ['Laravel', 'Led migration of a legacy app to Laravel', 'Experience'], ['Docker', 'Containerised services at current job', 'Experience'], ['Git', 'Listed in skills', 'Skills']],
                'partial' => [['REST APIs', 'Mentions "integrations" but no API detail', 'Experience']], 'missing' => ['CI/CD'],
                'skills' => ['PHP', 'Laravel', 'Docker', 'MySQL', 'Redis', 'Git'],
                'summary' => 'Most experienced applicant with solid evidence for core skills. API work is implied, not stated.'],
            ['id' => 13, 'name' => 'Funke Adeyemi', 'headline' => 'Full-stack Developer', 'location' => 'Ibadan', 'years' => 3, 'score' => 76, 'status' => 'Reviewing', 'applied' => '10 Nov 2026',
                'breakdown' => ['skills' => 74, 'experience' => 80, 'education' => 78, 'keywords' => 70],
                'matched' => [['PHP', 'Used in two freelance projects', 'Experience'], ['MySQL', 'Designed schemas for client apps', 'Experience'], ['Git', 'Listed in skills', 'Skills']],
                'partial' => [['Laravel', 'Mentions "PHP frameworks"; Laravel not named', 'Skills'], ['REST APIs', 'Built an API with Node.js, not PHP', 'Experience']], 'missing' => ['Docker', 'CI/CD'],
                'skills' => ['PHP', 'JavaScript', 'Node.js', 'React', 'MySQL', 'Git'],
                'summary' => 'Good all-round developer. Laravel is not explicit on the CV; worth asking at interview.'],
            ['id' => 14, 'name' => 'Ibrahim Musa', 'headline' => 'Web Developer', 'location' => 'Lagos', 'years' => 2, 'score' => 68, 'status' => 'New', 'applied' => 'Today',
                'breakdown' => ['skills' => 66, 'experience' => 65, 'education' => 74, 'keywords' => 68],
                'matched' => [['PHP', 'Listed in skills', 'Skills'], ['MySQL', 'Used in a school management project', 'Projects']],
                'partial' => [['Laravel', 'One personal project, no work experience', 'Projects']], 'missing' => ['REST APIs', 'Docker', 'CI/CD'],
                'skills' => ['PHP', 'Laravel', 'MySQL', 'HTML/CSS'],
                'summary' => 'Foundations are there. Limited evidence of professional use.'],
            ['id' => 15, 'name' => 'Chiamaka Eze', 'headline' => 'Software Engineer', 'location' => 'Enugu', 'years' => 5, 'score' => 61, 'status' => 'New', 'applied' => 'Today',
                'breakdown' => ['skills' => 52, 'experience' => 84, 'education' => 80, 'keywords' => 50],
                'matched' => [['REST APIs', 'Designed public APIs at a fintech', 'Experience'], ['Git', 'Listed in skills', 'Skills']],
                'partial' => [['PHP', 'Used early in career; recent work is Java', 'Experience']], 'missing' => ['Laravel', 'MySQL', 'Docker'],
                'skills' => ['Java', 'Spring Boot', 'PostgreSQL', 'REST APIs', 'Git'],
                'summary' => 'Strong engineer, but mostly in a different stack. May need time to ramp up on Laravel.'],
            ['id' => 16, 'name' => 'Segun Alade', 'headline' => 'IT Support Officer', 'location' => 'Ogun', 'years' => 1, 'score' => 39, 'status' => 'Rejected', 'applied' => '9 Nov 2026',
                'breakdown' => ['skills' => 30, 'experience' => 35, 'education' => 55, 'keywords' => 38],
                'matched' => [['MySQL', 'Basic queries in support role', 'Experience']],
                'partial' => [], 'missing' => ['PHP', 'Laravel', 'REST APIs', 'Git'],
                'skills' => ['Networking', 'Windows Server', 'SQL basics'],
                'summary' => 'Background is mainly IT support. Little evidence of web development.',
                'reject_reason' => 'Does not meet the required skills'],
        ];
    }

    public static function applicants(): array
    {
        $w = array_column(self::criteria(), 'weight', 'key');

        return array_map(function ($a) use ($w) {
            $a['score'] = (int) round(array_sum(array_map(fn ($k) => $a['breakdown'][$k] * $w[$k], array_keys($w))) / array_sum($w));
            $a['initials'] = collect_initials($a['name']);
            $a['tier'] = $a['score'] >= 80 ? 'Strong match' : ($a['score'] >= 60 ? 'Good match' : 'Partial match');
            $a['email'] = strtolower(str_replace(' ', '.', $a['name'])).'@example.com';
            $a['cv'] = str_replace(' ', '_', $a['name']).'_CV.pdf';
            $a['experience'] = [['title' => $a['headline'], 'company' => 'Recent employer', 'period' => 'Last '.$a['years'].' years', 'description' => 'Full details come from the parsed CV.']];
            $a['education'] = [['degree' => 'B.Sc. / HND', 'school' => 'University or polytechnic', 'period' => 'Graduated']];
            $a['cover_note'] = 'I am excited about this role and believe my experience fits what you are looking for.';

            return $a;
        }, self::applicantRows());
    }

    public static function applicant(int $id): ?array
    {
        foreach (self::applicants() as $a) {
            if ($a['id'] === $id) {
                return $a;
            }
        }

        return null;
    }

    /** Applicants for a job, optionally filtered by stage, ranked using the employer's weights. */
    public static function ranked(int $jobId, string $stage = 'all', array $weights = []): array
    {
        $list = $jobId === 1 ? self::applicants() : [];
        $w = [];
        foreach (self::criteria() as $c) {
            $w[$c['key']] = isset($weights[$c['key']]) ? (int) $weights[$c['key']] : $c['weight'];
        }
        $total = array_sum($w) ?: 1;
        foreach ($list as &$a) {
            $a['score'] = (int) round(array_sum(array_map(fn ($k) => $a['breakdown'][$k] * $w[$k], array_keys($w))) / $total);
            $a['tier'] = $a['score'] >= 80 ? 'Strong match' : ($a['score'] >= 60 ? 'Good match' : 'Partial match');
        }
        unset($a);
        if ($stage !== 'all') {
            $list = array_filter($list, fn ($a) => $a['status'] === $stage);
        }
        usort($list, fn ($x, $y) => $y['score'] <=> $x['score']);

        return array_values($list);
    }

    public static function interviews(): array
    {
        return [
            ['id' => 1, 'applicant_id' => 11, 'name' => 'Adaeze Okafor', 'initials' => 'AO', 'job' => 'PHP / Laravel Developer', 'date' => 'Tue, 17 Nov 2026', 'time' => '10:00 AM', 'mode' => 'Video call', 'status' => 'Confirmed', 'upcoming' => true],
            ['id' => 2, 'applicant_id' => 12, 'name' => 'Emeka Nwosu', 'initials' => 'EN', 'job' => 'PHP / Laravel Developer', 'date' => 'Wed, 18 Nov 2026', 'time' => '2:00 PM', 'mode' => 'Video call', 'status' => 'Awaiting reply', 'upcoming' => true],
            ['id' => 3, 'applicant_id' => 13, 'name' => 'Funke Adeyemi', 'initials' => 'FA', 'job' => 'PHP / Laravel Developer', 'date' => 'Fri, 20 Nov 2026', 'time' => '11:30 AM', 'mode' => 'In person', 'status' => 'Confirmed', 'upcoming' => true],
            ['id' => 4, 'applicant_id' => 14, 'name' => 'Tola Bello', 'initials' => 'TB', 'job' => 'QA Tester', 'date' => '10 Sep 2026', 'time' => '9:00 AM', 'mode' => 'Video call', 'status' => 'Completed', 'upcoming' => false],
        ];
    }
}

function collect_initials(string $name): string
{
    $p = preg_split('/\s+/', trim($name));

    return strtoupper(substr($p[0], 0, 1).substr(end($p), 0, 1));
}
