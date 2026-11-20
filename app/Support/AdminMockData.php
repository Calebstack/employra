<?php

namespace App\Support;

/** TEMPORARY sample data for the admin area of the Employra GUI preview. Delete with routes/gui-preview.php. */
class AdminMockData
{
    public static function admin(): array
    {
        return ['name' => 'Employra Admin', 'first_name' => 'Admin', 'initials' => 'EA', 'role' => 'Super admin', 'email' => 'admin@employra.example'];
    }

    public static function notifications(): array
    {
        return [
            ['icon' => 'building-check', 'tone' => 'orange', 'text' => '3 companies are waiting for verification', 'time' => '1 hour ago'],
            ['icon' => 'flag', 'tone' => 'orange', 'text' => 'A job post was reported as misleading', 'time' => 'Today'],
            ['icon' => 'person-plus', 'tone' => 'green', 'text' => '42 new sign-ups this week', 'time' => 'Yesterday'],
        ];
    }

    public static function stats(): array
    {
        return ['users' => 1284, 'candidates' => 1102, 'employers' => 182, 'jobs' => 96, 'applications' => 3471, 'pending' => 3, 'reports' => 2,
            'deltas' => ['users' => 12, 'jobs' => 8, 'applications' => 21, 'pending' => -2]];
    }

    /** Application funnel for the dashboard. */
    public static function funnel(): array
    {
        return ['Submitted' => 3471, 'Reviewing' => 1820, 'Shortlisted' => 640, 'Interview' => 215, 'Offer' => 48];
    }

    public static function topCategories(): array
    {
        return ['Engineering' => 28, 'Sales & Marketing' => 19, 'Finance' => 14, 'Education' => 12, 'Healthcare' => 9];
    }

    public static function activity(): array
    {
        return [
            ['icon' => 'building-check', 'tone' => 'orange', 'text' => 'NovaPay registered and is waiting for verification', 'time' => '1 hour ago'],
            ['icon' => 'flag', 'tone' => 'orange', 'text' => 'Adaeze Okafor reported a job post', 'time' => '2 hours ago'],
            ['icon' => 'briefcase', 'tone' => 'green', 'text' => 'BrightPath Technologies published “PHP / Laravel Developer”', 'time' => '2 days ago'],
            ['icon' => 'person-plus', 'tone' => '', 'text' => '42 new candidates signed up this week', 'time' => 'This week'],
            ['icon' => 'patch-check', 'tone' => 'green', 'text' => 'You verified Greenfield Agro Ltd', 'time' => '2 months ago'],
        ];
    }

    public static function filterJobs(string $status = 'all', string $q = ''): array
    {
        return array_values(array_filter(self::jobs(), fn ($j) => ($status === 'all' || $j['status'] === $status)
            && ($q === '' || stripos($j['title'].' '.$j['company'], $q) !== false)));
    }

    public static function filterReports(string $status = 'all'): array
    {
        return array_values(array_filter(self::reports(), fn ($r) => $status === 'all' || $r['status'] === $status));
    }

    /** Weekly sign-ups for the simple bar chart. */
    public static function signups(): array
    {
        return ['Mon' => 28, 'Tue' => 41, 'Wed' => 35, 'Thu' => 52, 'Fri' => 47, 'Sat' => 19, 'Sun' => 14];
    }

    public static function users(): array
    {
        $rows = [
            [1, 'Adaeze Okafor', 'adaeze.okafor@example.com', 'Candidate', 'Active', '8 Nov 2026', 'Today'],
            [2, 'Tunde Bakare', 'tunde@brightpath.example', 'Employer', 'Active', '2 Oct 2026', 'Today'],
            [3, 'Emeka Nwosu', 'emeka.nwosu@example.com', 'Candidate', 'Active', '9 Nov 2026', 'Yesterday'],
            [4, 'Grace Olamide', 'grace@greenfieldagro.example', 'Employer', 'Active', '14 Sep 2026', '3 days ago'],
            [5, 'Segun Alade', 'segun.alade@example.com', 'Candidate', 'Suspended', '9 Nov 2026', '5 days ago'],
            [6, 'Chiamaka Eze', 'chiamaka.eze@example.com', 'Candidate', 'Active', '10 Nov 2026', 'Today'],
            [7, 'Yusuf Danjuma', 'yusuf@novapay.example', 'Employer', 'Pending', '12 Nov 2026', 'Today'],
            [8, 'Funke Adeyemi', 'funke.adeyemi@example.com', 'Candidate', 'Active', '10 Nov 2026', 'Yesterday'],
        ];

        return array_map(fn ($r) => ['id' => $r[0], 'name' => $r[1], 'initials' => strtoupper(substr($r[1], 0, 1).substr(strrchr($r[1], ' '), 1, 1)), 'email' => $r[2], 'role' => $r[3], 'status' => $r[4], 'joined' => $r[5], 'last_seen' => $r[6]], $rows);
    }

    public static function filterUsers(string $role = 'all', string $q = ''): array
    {
        return array_values(array_filter(self::users(), fn ($u) => ($role === 'all' || strtolower($u['role']) === $role)
            && ($q === '' || stripos($u['name'].' '.$u['email'], $q) !== false)));
    }

    public static function companies(): array
    {
        $rows = [
            [1, 'BrightPath Technologies', 'BP', '#1558d6', 'Software & IT', 'Verified', 2, 'Tunde Bakare', '2 Oct 2026'],
            [2, 'Greenfield Agro Ltd', 'GA', '#12a150', 'Agriculture', 'Verified', 3, 'Grace Olamide', '14 Sep 2026'],
            [3, 'NovaPay', 'NP', '#7a3cff', 'Finance', 'Pending', 0, 'Yusuf Danjuma', '12 Nov 2026'],
            [4, 'Lakeside Schools', 'LS', '#e5484d', 'Education', 'Pending', 0, 'Mrs. Ade Bola', '11 Nov 2026'],
            [5, 'QuickMove Logistics', 'QM', '#ff6a00', 'Logistics', 'Pending', 0, 'Ifeanyi Obi', '11 Nov 2026'],
            [6, 'Sunrise Clinics', 'SC', '#0891b2', 'Healthcare', 'Rejected', 0, 'Dr. K. Musa', '1 Nov 2026'],
        ];

        return array_map(function ($r) {
            $pending = $r[5] === 'Pending';
            $bad = $r[1] === 'Lakeside Schools';

            return ['id' => $r[0], 'name' => $r[1], 'initials' => $r[2], 'color' => $r[3], 'industry' => $r[4], 'status' => $r[5], 'jobs' => $r[6], 'owner' => $r[7], 'registered' => $r[8],
                'checks' => $pending ? [['Email domain matches website', ! $bad], ['Registration number provided', $r[1] !== 'QuickMove Logistics'], ['No scam reports', true]] : []];
        }, $rows);
    }

    public static function jobs(): array
    {
        $rows = [
            [1, 'PHP / Laravel Developer', 'BrightPath Technologies', 'Open', 6, '2 days ago', null],
            [2, 'Frontend Developer Intern', 'BrightPath Technologies', 'Open', 14, '2 days ago', null],
            [3, 'Farm Operations Manager', 'Greenfield Agro Ltd', 'Open', 9, '5 days ago', null],
            [4, 'Data Entry Clerk (pay a fee to apply)', 'QuickMove Logistics', 'Flagged', 31, '1 day ago', 'Asks candidates to pay a fee'],
            [5, 'Customer Support Agent', 'NovaPay', 'Open', 12, '3 days ago', null],
            [6, 'Secondary School Teacher', 'Lakeside Schools', 'Closed', 18, '1 month ago', null],
        ];

        return array_map(fn ($r) => ['id' => $r[0], 'title' => $r[1], 'company' => $r[2], 'status' => $r[3], 'applicants' => $r[4], 'posted' => $r[5], 'flag' => $r[6]], $rows);
    }

    public static function reports(): array
    {
        return [
            ['id' => 1, 'type' => 'Job post', 'target' => 'Data Entry Clerk (pay a fee to apply)', 'reason' => 'Asks candidates to pay a fee', 'by' => 'Adaeze Okafor', 'when' => '2 hours ago', 'status' => 'Open', 'severity' => 'High', 'detail' => 'The job description says applicants must send ₦5,000 for “registration”. This looks like a scam.'],
            ['id' => 2, 'type' => 'Candidate', 'target' => 'Segun Alade', 'reason' => 'Fake qualifications', 'by' => 'QuickMove Logistics', 'when' => 'Yesterday', 'status' => 'Open', 'severity' => 'Medium', 'detail' => 'Certificate listed on the CV could not be verified with the school.'],
            ['id' => 3, 'type' => 'Company', 'target' => 'Sunrise Clinics', 'reason' => 'Impersonation', 'by' => 'System check', 'when' => '3 days ago', 'status' => 'Resolved', 'severity' => 'Low', 'detail' => 'Company name matches a registered clinic but the email domain does not.'],
        ];
    }

    /** Transparency log: AI suggestions vs human decisions. No personal protected traits are ever shown. */
    public static function audit(): array
    {
        return [
            ['when' => 'Today 10:12', 'who' => 'Tunde Bakare', 'action' => 'Shortlisted', 'target' => 'Emeka Nwosu · PHP / Laravel Developer', 'ai' => 'Ranked #2 (84)', 'agreed' => true],
            ['when' => 'Today 09:40', 'who' => 'Tunde Bakare', 'action' => 'Changed ranking weights', 'target' => 'PHP / Laravel Developer', 'ai' => 'Skills 40 → 30, Experience 30 → 40', 'agreed' => null],
            ['when' => 'Yesterday', 'who' => 'Grace Olamide', 'action' => 'Declined', 'target' => 'Candidate · Farm Operations Manager', 'ai' => 'Ranked #5 (41)', 'agreed' => true],
            ['when' => 'Yesterday', 'who' => 'Grace Olamide', 'action' => 'Shortlisted', 'target' => 'Candidate · Farm Operations Manager', 'ai' => 'Ranked #6 (38)', 'agreed' => false],
            ['when' => '3 days ago', 'who' => 'System', 'action' => 'CV analysis completed', 'target' => 'Adaeze_Okafor_CV_2026.pdf', 'ai' => 'Readiness 82', 'agreed' => null],
        ];
    }

    public static function auditStats(): array
    {
        return ['analyses' => 2310, 'agree' => 81, 'overrides' => 19, 'appeals' => 4];
    }
}
