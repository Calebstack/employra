<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Employra

Employra is a recruitment platform that connects candidates with companies. Candidates can find roles and apply, companies can manage vacancies and applications, and administrators can oversee activity across the platform.

## Employra GUI preview

Run the app locally with `php artisan serve`, then open `http://127.0.0.1:8000/gui-preview`. The preview page links to the public, candidate, company, and administrator screens so you can explore the interface.

The preview uses sample data for visual examples. Public job and company listings use database records and show open jobs from verified companies.

## Main features

- **Candidates:** create an account, complete a profile, upload and reuse a CV, search job listings, apply, follow application updates, and respond to interview invitations.
- **Companies:** register and await administrator verification, publish and manage jobs, review applicants, update application stages, and arrange interviews.
- **Administrators:** manage candidate accounts, verify and manage companies, moderate job listings, review reports, and view application and audit activity.

Applications and their status history are stored in the database. CV files and profile photos are stored privately; company access to a CV is limited to the CV attached to the relevant application.

## Run locally

Requirements: PHP 8.2 or newer, Composer, Node.js/npm, and a database configured for the application.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set the database connection in `.env`, then run:

```bash
php artisan migrate
npm install
npm run build
php artisan serve
```

Create an administrator account from the command line:

```bash
php artisan employra:admin:create admin@example.com
```

The command prompts for the administrator's name and password. Public registration does not create administrator accounts.

## Not currently available

CV analysis, saved-job bookmarking, notification delivery, subscriptions, and payments are not implemented.
