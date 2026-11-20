<?php

namespace App\Support;

/**
 * TEMPORARY sample data for the Employra GUI preview.
 *
 * Every Blade view in this package reads plain arrays shaped like the ones below
 * (e.g. $jobs, $job, $companies, $company). When the real backend exists, return
 * the same keys from your controllers/Eloquent resources and delete this file
 * together with routes/gui-preview.php.
 */
class MockData
{
    public static function categories(): array
    {
        return ['Engineering', 'Design', 'Marketing', 'Sales', 'Finance', 'Operations', 'Customer Support', 'Human Resources', 'Data & AI'];
    }

    public static function jobTypes(): array
    {
        return ['Full-time', 'Part-time', 'Contract', 'Internship'];
    }

    public static function workModes(): array
    {
        return ['On-site', 'Hybrid', 'Remote'];
    }

    public static function levels(): array
    {
        return ['Entry level', 'Mid level', 'Senior', 'Lead / Manager'];
    }

    public static function companies(): array
    {
        $rows = [
            ['slug' => 'brightpath-tech', 'name' => 'BrightPath Technologies', 'initials' => 'BP', 'color' => '#1558d6', 'industry' => 'Software & IT', 'location' => 'Lagos, Nigeria', 'size' => '51–200 employees', 'founded' => 2016, 'website' => 'brightpath.example',
                'short' => 'Cloud software and custom web platforms for banks, schools and growing businesses.',
                'about' => 'BrightPath builds dependable web and mobile platforms for organisations across West Africa. Our teams ship in small, focused squads and care about clean code, clear communication and long-term maintenance, not just launch day.'],
            ['slug' => 'greenfield-agro', 'name' => 'Greenfield Agro Ltd', 'initials' => 'GA', 'color' => '#12a150', 'industry' => 'Agriculture', 'location' => 'Ibadan, Nigeria', 'size' => '201–500 employees', 'founded' => 2009, 'website' => 'greenfieldagro.example',
                'short' => 'Farm-to-market produce, processing and logistics serving retailers nationwide.',
                'about' => 'Greenfield Agro connects smallholder farmers to retailers through processing, storage and a reliable delivery network. We are investing in data and digital tools to make supply chains more transparent.'],
            ['slug' => 'novapay', 'name' => 'NovaPay', 'initials' => 'NP', 'color' => '#7a3cff', 'industry' => 'Fintech', 'location' => 'Lagos, Nigeria', 'size' => '51–200 employees', 'founded' => 2019, 'website' => 'novapay.example',
                'short' => 'Payments and savings tools for small businesses and everyday customers.',
                'about' => 'NovaPay helps small businesses collect payments, manage cash flow and save with confidence. Our product, risk and engineering teams work closely together and ship improvements every week.'],
            ['slug' => 'lumina-health', 'name' => 'Lumina Health', 'initials' => 'LH', 'color' => '#e5484d', 'industry' => 'Healthcare', 'location' => 'Abuja, Nigeria', 'size' => '201–500 employees', 'founded' => 2012, 'website' => 'luminahealth.example',
                'short' => 'Clinics, diagnostics and telehealth with a focus on accessible primary care.',
                'about' => 'Lumina Health runs a network of clinics and a telehealth service that makes primary care easier to reach. We hire people who care about patients and enjoy solving practical operational problems.'],
            ['slug' => 'pixelcraft-studio', 'name' => 'Pixelcraft Studio', 'initials' => 'PS', 'color' => '#ff6a00', 'industry' => 'Creative & Media', 'location' => 'Remote (Africa)', 'size' => '11–50 employees', 'founded' => 2020, 'website' => 'pixelcraft.example',
                'short' => 'A remote-first design and brand studio working with startups across Africa and Europe.',
                'about' => 'Pixelcraft is a small remote-first studio that designs brands, websites and product interfaces. We value craft, honest feedback and flexible working hours.'],
            ['slug' => 'summit-logistics', 'name' => 'Summit Logistics', 'initials' => 'SL', 'color' => '#0a1f6e', 'industry' => 'Logistics', 'location' => 'Mowe, Ogun State', 'size' => '201–500 employees', 'founded' => 2011, 'website' => 'summitlogistics.example',
                'short' => 'Warehousing, last-mile delivery and fleet management along the Lagos–Ibadan corridor.',
                'about' => 'Summit Logistics moves goods across the Lagos–Ibadan corridor with a growing fleet, modern warehouses and live tracking. We are building an operations team that is as strong with data as it is on the road.'],
            ['slug' => 'oakridge-academy', 'name' => 'Oakridge Academy', 'initials' => 'OA', 'color' => '#0f9d9a', 'industry' => 'Education', 'location' => 'Ogun State, Nigeria', 'size' => '51–200 employees', 'founded' => 2005, 'website' => 'oakridge.example',
                'short' => 'A private school group with a strong STEM programme and modern learning tools.',
                'about' => 'Oakridge Academy educates students from nursery to senior secondary with a focus on STEM, character and technology. We hire teachers, administrators and support staff who love learning.'],
            ['slug' => 'harvest-bank', 'name' => 'Harvest Microfinance Bank', 'initials' => 'HM', 'color' => '#b54700', 'industry' => 'Banking & Finance', 'location' => 'Abeokuta, Nigeria', 'size' => '51–200 employees', 'founded' => 2008, 'website' => 'harvestmfb.example',
                'short' => 'Community banking, loans and savings for traders, artisans and families.',
                'about' => 'Harvest Microfinance Bank serves traders, artisans and families with simple savings and loan products. We are modernising our customer journey and looking for people who understand both finance and people.'],
        ];

        $jobCounts = [];
        foreach (self::rawJobs() as $j) {
            $jobCounts[$j['company_slug']] = ($jobCounts[$j['company_slug']] ?? 0) + 1;
        }
        foreach ($rows as &$c) {
            $c['open_roles'] = $jobCounts[$c['slug']] ?? 0;
        }

        return $rows;
    }

    public static function company(string $slug): ?array
    {
        foreach (self::companies() as $c) {
            if ($c['slug'] === $slug) {
                return $c;
            }
        }

        return null;
    }

    /** Jobs with their company data merged in. */
    public static function jobs(): array
    {
        $companies = [];
        foreach (self::companies() as $c) {
            $companies[$c['slug']] = $c;
        }

        return array_map(function ($j) use ($companies) {
            $c = $companies[$j['company_slug']];
            $j['company'] = $c['name'];
            $j['company_initials'] = $c['initials'];
            $j['company_color'] = $c['color'];
            $j['company_industry'] = $c['industry'];

            return $j;
        }, self::rawJobs());
    }

    public static function job(string $slug): ?array
    {
        foreach (self::jobs() as $j) {
            if ($j['slug'] === $slug) {
                return $j;
            }
        }

        return null;
    }

    public static function jobsForCompany(string $companySlug): array
    {
        return array_values(array_filter(self::jobs(), fn ($j) => $j['company_slug'] === $companySlug));
    }

    public static function similarJobs(array $job, int $limit = 3): array
    {
        $out = array_filter(self::jobs(), fn ($j) => $j['slug'] !== $job['slug'] && $j['category'] === $job['category']);
        if (count($out) < $limit) {
            $out += array_filter(self::jobs(), fn ($j) => $j['slug'] !== $job['slug']);
        }

        return array_slice(array_values($out), 0, $limit);
    }

    /** Simple filter used by the preview routes so the search form really works. */
    public static function filterJobs(array $q): array
    {
        $jobs = self::jobs();
        $kw = strtolower(trim($q['q'] ?? ''));
        $loc = strtolower(trim($q['location'] ?? ''));
        $requestedState = $q['state'] ?? '';
        $state = is_string($requestedState) ? NigerianStates::normalize($requestedState) : null;
        $salaryMin = isset($q['salary_min']) && $q['salary_min'] !== '' ? (int) $q['salary_min'] : null;
        $salaryMax = isset($q['salary_max']) && $q['salary_max'] !== '' ? (int) $q['salary_max'] : null;
        if ($salaryMin === 0) {
            $salaryMin = null;
        }
        if ($salaryMax === 10000000) {
            $salaryMax = null;
        }
        if (is_string($requestedState) && trim($requestedState) !== '' && $state === null) {
            return [];
        }

        return array_values(array_filter($jobs, function ($j) use ($q, $kw, $loc, $state, $salaryMin, $salaryMax) {
            if ($salaryMin !== null || $salaryMax !== null) {
                $jobSalaryMin = $j['salary_min'] ?? null;
                $jobSalaryMax = $j['salary_max'] ?? null;
                if (! is_numeric($jobSalaryMin) || ! is_numeric($jobSalaryMax)) {
                    return false;
                }
                if ($salaryMin !== null && $jobSalaryMax < $salaryMin) {
                    return false;
                }
                if ($salaryMax !== null && $jobSalaryMin > $salaryMax) {
                    return false;
                }
            }

            if ($kw !== '') {
                $hay = strtolower($j['title'].' '.$j['company'].' '.implode(' ', $j['skills']).' '.$j['category']);
                if (! str_contains($hay, $kw)) {
                    return false;
                }
            }
            if ($state !== null && $j['state'] !== $state && $j['mode'] !== 'Remote') {
                return false;
            }
            if ($loc !== '' && ! str_contains(strtolower($j['location'].' '.$j['mode']), $loc)) {
                return false;
            }
            foreach (['category' => 'category', 'type' => 'type', 'mode' => 'mode', 'level' => 'level'] as $param => $key) {
                $val = $q[$param] ?? '';
                if (is_array($val) ? ($val && ! in_array($j[$key], $val, true)) : ($val !== '' && $j[$key] !== $val)) {
                    return false;
                }
            }

            return true;
        }));
    }

    public static function filterCompanies(array $q): array
    {
        $kw = strtolower(trim($q['q'] ?? ''));
        $ind = $q['industry'] ?? '';

        return array_values(array_filter(self::companies(), function ($c) use ($kw, $ind) {
            if ($kw !== '' && ! str_contains(strtolower($c['name'].' '.$c['industry'].' '.$c['location']), $kw)) {
                return false;
            }

            return $ind === '' || $c['industry'] === $ind;
        }));
    }

    public static function industries(): array
    {
        return array_values(array_unique(array_column(self::companies(), 'industry')));
    }

    public static function stats(): array
    {
        return [
            ['value' => '2,400+', 'label' => 'Open roles'],
            ['value' => '850+', 'label' => 'Hiring companies'],
            ['value' => '38k+', 'label' => 'CVs reviewed'],
            ['value' => '4 days', 'label' => 'Average time to first response'],
        ];
    }

    private static function rawJobs(): array
    {
        return [
            ['slug' => 'php-laravel-developer-brightpath', 'title' => 'PHP / Laravel Developer', 'company_slug' => 'brightpath-tech', 'location' => 'Lagos, Nigeria', 'state' => 'Lagos', 'mode' => 'Hybrid', 'type' => 'Full-time', 'level' => 'Mid level', 'category' => 'Engineering', 'salary' => '₦450k – ₦700k / month', 'salary_min' => 450000, 'salary_max' => 700000, 'posted' => '2 days ago', 'deadline' => '30 Nov 2026',
                'skills' => ['PHP', 'Laravel', 'MySQL', 'REST APIs', 'Git'],
                'summary' => 'Build and maintain Laravel applications for banking and education clients as part of a friendly product squad.',
                'responsibilities' => ['Design, build and test new features in Laravel 11/12', 'Write clean, documented REST APIs for web and mobile clients', 'Review code and mentor junior developers', 'Work with designers and product managers to refine requirements', 'Investigate and fix production issues quickly and calmly'],
                'must' => ['2+ years of professional PHP and Laravel experience', 'Strong MySQL skills, including query optimisation', 'Comfortable with Git and pull-request workflows', 'Clear written and spoken English'],
                'nice' => ['Docker and basic CI/CD experience', 'Experience with queues and scheduled jobs', 'Familiarity with AWS'],
                'benefits' => ['Health insurance', 'Learning budget', 'Flexible hybrid schedule', 'Annual bonus']],
            ['slug' => 'ui-ux-designer-pixelcraft', 'title' => 'UI/UX Designer', 'company_slug' => 'pixelcraft-studio', 'location' => 'Remote (Africa)', 'state' => null, 'mode' => 'Remote', 'type' => 'Full-time', 'level' => 'Mid level', 'category' => 'Design', 'salary' => '$1,200 – $1,800 / month', 'salary_min' => null, 'salary_max' => null, 'posted' => '3 days ago', 'deadline' => '25 Nov 2026',
                'skills' => ['Figma', 'UX Research', 'Design Systems', 'Prototyping'],
                'summary' => 'Design websites and product interfaces for startup clients, from first sketch to developer hand-off.',
                'responsibilities' => ['Lead discovery and turn findings into wireframes and prototypes', 'Create and maintain design systems in Figma', 'Present design decisions to clients with clear reasoning', 'Collaborate with developers to ensure faithful implementation'],
                'must' => ['3+ years designing web or mobile products', 'A portfolio showing process, not only final screens', 'Strong Figma skills', 'Good communication across time zones'],
                'nice' => ['Basic HTML/CSS knowledge', 'Motion design skills', 'Brand identity experience'],
                'benefits' => ['Fully remote', 'Flexible hours', 'Equipment allowance', 'Paid time off']],
            ['slug' => 'junior-data-analyst-novapay', 'title' => 'Junior Data Analyst', 'company_slug' => 'novapay', 'location' => 'Lagos, Nigeria', 'state' => 'Lagos', 'mode' => 'Hybrid', 'type' => 'Full-time', 'level' => 'Entry level', 'category' => 'Data & AI', 'salary' => '₦300k – ₦420k / month', 'salary_min' => 300000, 'salary_max' => 420000, 'posted' => '1 day ago', 'deadline' => '05 Dec 2026',
                'skills' => ['SQL', 'Excel', 'Power BI', 'Python'],
                'summary' => 'Turn payment and customer data into clear reports that help product and risk teams make better decisions.',
                'responsibilities' => ['Build weekly and monthly dashboards for product and risk teams', 'Write SQL queries to answer ad-hoc business questions', 'Clean and validate data from multiple sources', 'Document metrics so everyone uses the same definitions'],
                'must' => ['B.Sc. in a numerate discipline or equivalent experience', 'Solid SQL and spreadsheet skills', 'Curiosity and attention to detail'],
                'nice' => ['Python (pandas) knowledge', 'Experience with Power BI or Metabase', 'Interest in fintech'],
                'benefits' => ['Health insurance', 'Mentorship programme', 'Learning budget']],
            ['slug' => 'digital-marketing-executive-greenfield', 'title' => 'Digital Marketing Executive', 'company_slug' => 'greenfield-agro', 'location' => 'Ibadan, Nigeria', 'state' => 'Oyo', 'mode' => 'On-site', 'type' => 'Full-time', 'level' => 'Entry level', 'category' => 'Marketing', 'salary' => '₦250k – ₦350k / month', 'salary_min' => 250000, 'salary_max' => 350000, 'posted' => '5 days ago', 'deadline' => '20 Nov 2026',
                'skills' => ['Social Media', 'Content Writing', 'SEO', 'Canva'],
                'summary' => 'Grow our brand and retailer relationships through social media, email and storytelling about farmers and food.',
                'responsibilities' => ['Plan and publish content across social channels', 'Write blog posts and newsletters', 'Track campaign results and report on them monthly', 'Coordinate photo and video shoots with the field team'],
                'must' => ['1+ year of social media or content experience', 'Excellent writing skills', 'Comfortable with Canva or similar tools'],
                'nice' => ['Basic SEO knowledge', 'Photography or video editing'],
                'benefits' => ['Staff lunch', 'Transport allowance', 'Annual bonus']],
            ['slug' => 'clinic-operations-manager-lumina', 'title' => 'Clinic Operations Manager', 'company_slug' => 'lumina-health', 'location' => 'Abuja, Nigeria', 'state' => 'Federal Capital Territory', 'mode' => 'On-site', 'type' => 'Full-time', 'level' => 'Lead / Manager', 'category' => 'Operations', 'salary' => '₦600k – ₦850k / month', 'salary_min' => 600000, 'salary_max' => 850000, 'posted' => '1 week ago', 'deadline' => '15 Dec 2026',
                'skills' => ['Operations', 'Team Leadership', 'Healthcare Admin', 'Budgeting'],
                'summary' => 'Lead day-to-day operations across two clinics, keeping patients, staff and suppliers running smoothly.',
                'responsibilities' => ['Manage clinic staff schedules and performance', 'Monitor budgets, stock levels and supplier performance', 'Improve patient flow and satisfaction scores', 'Ensure compliance with health regulations'],
                'must' => ['5+ years in operations, ideally in healthcare', 'Proven team leadership', 'Strong organisational and budgeting skills'],
                'nice' => ['MBA or healthcare management qualification', 'Experience with clinic software'],
                'benefits' => ['Health insurance', 'Pension', 'Performance bonus']],
            ['slug' => 'fleet-coordinator-summit', 'title' => 'Fleet & Dispatch Coordinator', 'company_slug' => 'summit-logistics', 'location' => 'Mowe, Ogun State', 'state' => 'Ogun', 'mode' => 'On-site', 'type' => 'Full-time', 'level' => 'Mid level', 'category' => 'Operations', 'salary' => '₦280k – ₦380k / month', 'salary_min' => 280000, 'salary_max' => 380000, 'posted' => '4 days ago', 'deadline' => '28 Nov 2026',
                'skills' => ['Dispatch', 'Route Planning', 'Excel', 'Communication'],
                'summary' => 'Coordinate drivers, vehicles and deliveries along the Lagos–Ibadan corridor.',
                'responsibilities' => ['Assign drivers and vehicles to daily delivery routes', 'Track deliveries and update customers on progress', 'Report delays and incidents promptly', 'Maintain accurate dispatch records'],
                'must' => ['2+ years in logistics or dispatch', 'Calm under pressure', 'Good Excel skills'],
                'nice' => ['Experience with fleet-tracking software', 'Basic knowledge of road regulations'],
                'benefits' => ['Transport allowance', 'Lunch', 'Overtime pay']],
            ['slug' => 'mathematics-teacher-oakridge', 'title' => 'Mathematics Teacher (Senior Secondary)', 'company_slug' => 'oakridge-academy', 'location' => 'Ogun State, Nigeria', 'state' => 'Ogun', 'mode' => 'On-site', 'type' => 'Full-time', 'level' => 'Mid level', 'category' => 'Human Resources', 'salary' => '₦220k – ₦320k / month', 'salary_min' => 220000, 'salary_max' => 320000, 'posted' => '6 days ago', 'deadline' => '10 Dec 2026',
                'skills' => ['Mathematics', 'Lesson Planning', 'Classroom Management'],
                'summary' => 'Teach mathematics to senior secondary students and help them prepare confidently for WAEC and university.',
                'responsibilities' => ['Plan and deliver engaging mathematics lessons', 'Assess progress and give constructive feedback', 'Support students who need extra help', 'Take part in school events and parent meetings'],
                'must' => ['B.Sc./B.Ed. in Mathematics or related field', '2+ years of teaching experience', 'Passion for helping students succeed'],
                'nice' => ['Experience with online learning tools', 'Experience coaching math competitions'],
                'benefits' => ['Staff housing support', 'Health insurance', 'Professional development']],
            ['slug' => 'customer-support-agent-novapay', 'title' => 'Customer Support Agent', 'company_slug' => 'novapay', 'location' => 'Remote (Nigeria)', 'state' => null, 'mode' => 'Remote', 'type' => 'Contract', 'level' => 'Entry level', 'category' => 'Customer Support', 'salary' => '₦200k – ₦260k / month', 'salary_min' => 200000, 'salary_max' => 260000, 'posted' => 'Today', 'deadline' => '02 Dec 2026',
                'skills' => ['Customer Service', 'Communication', 'Zendesk', 'Problem Solving'],
                'summary' => 'Help customers resolve payment questions quickly and kindly over chat and email.',
                'responsibilities' => ['Respond to customer chats and emails within target times', 'Escalate complex issues to the right team', 'Document recurring problems and suggest improvements'],
                'must' => ['Excellent written English', 'Patience and empathy', 'Reliable internet and a quiet workspace'],
                'nice' => ['Previous support experience', 'Familiarity with Zendesk or Freshdesk'],
                'benefits' => ['Remote work', 'Paid training', 'Performance bonus']],
            ['slug' => 'loan-officer-harvest', 'title' => 'Loan Officer', 'company_slug' => 'harvest-bank', 'location' => 'Abeokuta, Nigeria', 'state' => 'Ogun', 'mode' => 'On-site', 'type' => 'Full-time', 'level' => 'Mid level', 'category' => 'Finance', 'salary' => '₦260k – ₦380k / month', 'salary_min' => 260000, 'salary_max' => 380000, 'posted' => '1 week ago', 'deadline' => '12 Dec 2026',
                'skills' => ['Credit Analysis', 'Customer Relations', 'Microfinance', 'Excel'],
                'summary' => 'Assess and manage small-business loans while building trusted relationships with customers.',
                'responsibilities' => ['Visit customers and assess loan applications', 'Prepare credit memos for approval committee', 'Monitor repayments and follow up respectfully', 'Grow the loan portfolio within agreed risk limits'],
                'must' => ['2+ years in banking or microfinance', 'Strong numeracy and judgement', 'Ability to travel locally'],
                'nice' => ['Knowledge of Yoruba', 'Experience with core banking software'],
                'benefits' => ['Pension', 'Health insurance', 'Commission']],
            ['slug' => 'frontend-intern-brightpath', 'title' => 'Frontend Developer Intern', 'company_slug' => 'brightpath-tech', 'location' => 'Lagos, Nigeria', 'state' => 'Lagos', 'mode' => 'Hybrid', 'type' => 'Internship', 'level' => 'Entry level', 'category' => 'Engineering', 'salary' => '₦100k – ₦150k / month', 'salary_min' => 100000, 'salary_max' => 150000, 'posted' => '2 days ago', 'deadline' => '01 Dec 2026',
                'skills' => ['HTML', 'CSS', 'JavaScript', 'Bootstrap'],
                'summary' => 'A 6-month paid internship building interfaces with senior mentors and a real product team.',
                'responsibilities' => ['Build responsive pages from design files', 'Fix interface bugs reported by users', 'Pair with senior developers on weekly tasks', 'Present your work at the monthly demo day'],
                'must' => ['Good grasp of HTML, CSS and JavaScript', 'A portfolio or GitHub with small projects', 'Eagerness to learn'],
                'nice' => ['Experience with Bootstrap or Tailwind', 'Basic Git knowledge'],
                'benefits' => ['Mentorship', 'Possible full-time offer', 'Laptop provided']],
        ];
    }
}
