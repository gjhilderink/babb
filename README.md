# BABB Portaal

Business club administration portal built with Laravel 11.

## Features

### Leden
- Member directory with contact details, company, address, and status (actief / inactief / geschorst)
- Membership types with pricing and billing cycles
- CSV export (UTF-8 BOM, semicolon-delimited — Excel compatible)
- CSV import with upsert on e-mail address
- Expiring memberships shown on the dashboard

### Leads (potentiële leden)
- Track prospective members with source, status, and follow-up assignment
- Record who referred the lead (existing member or free text)
- One-click conversion to full member
- Active leads shown on the dashboard

### Facturen
- Create invoices with line items, VAT rates, and due dates
- Statuses: concept → verzonden → betaald (+ verlopen)
- PDF export via DomPDF
- Mark paid / mark sent actions with role restrictions
- Membership billing batch: generate invoices for all active members in one action

### Evenementen
- Event planning with date, location, status (concept / bevestigd)
- Task list per event with open/done status
- Upcoming events shown on the dashboard

### Gebruikersbeheer
- Three roles: **admin** (everything), **bestuur** (everything except sending invoices), **gebruiker** (dashboard events only)
- Admin can create, edit, and delete portal users

### Instellingen (admin only)
- Upload a custom logo shown in the navigation bar
- Upload a background image displayed behind the portal with a dark overlay

## Roles

| Actie | Admin | Bestuur | Gebruiker |
|---|:---:|:---:|:---:|
| Dashboard | ✓ | ✓ | ✓ (events only) |
| Leden, leads, evenementen, facturen | ✓ | ✓ | — |
| Factuur verzenden | ✓ | — | — |
| Gebruikersbeheer & instellingen | ✓ | — | — |

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB (or SQLite for local dev)

## Installation

```bash
git clone https://github.com/gjhilderink/babb.git
cd babb
composer install
cp .env.example .env
php artisan key:generate
# Configure DB in .env, then:
php artisan migrate --seed
php artisan serve
```

The seeder creates a default admin user — update the credentials in `database/seeders/DatabaseSeeder.php` before deploying.

## Tech stack

- Laravel 11 (minimal scaffold)
- Blade templates + Tailwind CSS (CDN, with explicit `<style>` block for custom brand colours)
- Alpine.js (CDN) — hamburger menu, reactive UI
- barryvdh/laravel-dompdf — PDF invoice export
- Role middleware (`role:admin`, `role:admin,bestuur`) registered in `bootstrap/app.php`
