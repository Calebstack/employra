<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Employra GUI preview

Start the application with `php artisan serve` and open `/`. Public job and company pages show stored
listings from verified companies; the separate `/gui-preview` page still contains sample UI data.

Login, candidate registration, company registration, and logout are connected to Laravel authentication.
Candidate accounts live in `users`; company email and password hashes live in `companies`; administrator
accounts live in `admins`. The account-separation migration preserves existing employer/admin credentials
and rebinds the related audit records. The migration clears the old `company_user` links; the configured
local database account cannot drop tables, so the now-empty table remains without foreign keys. Candidate
registration and login route incomplete candidates through onboarding, where preferences and profile
details are saved to the candidate profile.

Company registration and sign-in land on the company workspace. Pending companies cannot post jobs until an
administrator verifies them from Admin → Companies. Create the first administrator with
`php artisan employra:admin:create admin@example.com`; the command prompts for a name and password, and
public registration cannot create administrator accounts.

Admin company verification, job moderation, company/job reports, candidate user actions, platform settings,
and the activity log use database records. Candidates can report a company profile or a job. Applications
store the candidate, job, optional selected CV, cover note, and snapshots of availability and expected salary.
Every submission and later status change is recorded in application history.

Companies can review applicants, access only the CV selected for that application, move candidates through
reviewing/shortlisted/interview/offer/rejected statuses, and schedule interviews. Candidates can view
application history, confirm an upcoming interview or request a reschedule, and withdraw an active
application. Admins can inspect candidate profiles, selected CV metadata, application history, and interview
responses.

CV upload is optional during onboarding. PDF and DOCX files up to 5 MB and profile photos up to 2 MB are
stored on Laravel's private local disk. Candidates can reuse and download their own CVs; company access is
restricted to the CV attached to a specific application. Admin CV downloads are logged. Admin company
details include profile fields, jobs, applications, and reports. Permanent company deletion removes its
jobs, related applications and reports, and company/job-specific audit entries, while preserving unrelated
candidate accounts and CVs and retaining a generic deletion audit event. Run `php artisan migrate` when
setting up a new database.

CV analysis, saved-job bookmarking, notification delivery, subscriptions, and payments are not implemented
yet. The demo seeder remains available for local experiments and creates sample credentials only in
local/testing environments; do not run it when you want a clean local listings database.
