# BABB Portaal

Business club administration portal built with Laravel 11.

## Features

### Leden
- Member directory with contact details, company, address, status (actief / inactief / geschorst)
- Separate private address (straat, postcode, stad) per member
- Separate invoice e-mail address (`factuur_email`) — falls back to main e-mail
- Membership types with pricing and billing cycles
- CSV export (UTF-8 BOM, semicolon-delimited — Excel compatible)
- CSV import with upsert on e-mail address
- Expiring memberships shown on the dashboard

### Leads (potentiële leden)
- Track prospective members with source, status (nieuw / in contact / follow-up / gewonnen / verloren), and priority
- Record who referred the lead (existing member or free text)
- One-click conversion to full member
- Active leads shown on the dashboard
- Assigned leads appear as tasks in the **Taken** overview

### Taken
- Create and assign tasks to users with priority (hoog / normaal / laag), deadline, and status (open / bezig / gereed)
- Inline status and priority dropdowns — change without leaving the list
- Tasks can be linked to a meeting (created from the meeting detail page)
- Three task sources unified in one view:
  - **Standalone / meeting tasks** — fully editable
  - **Lead opvolging** — leads assigned to a user, with inline status and priority
  - **Event tasks** — tasks from events, with inline status and priority
- **Nav badge** shows count of your own open tasks; turns **red** when any are overdue
- **Daily reminder emails** (08:00) sent to users with overdue tasks — includes task list with deadline, priority, and a direct link

### Vergaderingen
- Meeting log with date, location, agenda, and status (gepland / afgerond / geannuleerd)
- Per-user notes — each participant writes their own notes, all notes visible together
- Task management per meeting: add, status-toggle, and edit tasks directly from the meeting page
- ACL-controlled: `meetings.view` and `meetings.manage`

### Evenementen
- Event planning with date, location, status (concept / bevestigd)
- Task list per event with status (open / bezig / gereed) and priority
- Budget tracking with cost lines and receipt uploads
- Upcoming events shown on the dashboard

### Financieel
- **Facturen** — create invoices with line items, VAT rates, and due dates; statuses: concept → verzonden → betaald (+ verlopen); PDF export via DomPDF; membership billing batch
- **Contributies** — generate membership invoices for all active members in one action
- **Afdracht Bonboys** — record transfers to Bonboys with amount, subject, date, notes, and status (nieuw / nog te betalen / betaald); running totals for paid and outstanding amounts

### Gebruikersbeheer
- Three roles: **admin** (everything), **bestuur** (everything except sending invoices), **gebruiker** (dashboard + taken)
- Admin can create, edit, and delete portal users
- ACL system for fine-grained permission overrides per user

### Instellingen (admin only)
- Upload a custom logo shown in the navigation bar
- Upload a background image displayed behind the portal with a dark overlay

## Roles

| Actie | Admin | Bestuur | Gebruiker |
|---|:---:|:---:|:---:|
| Dashboard | ✓ | ✓ | ✓ |
| Leden, leads, evenementen, facturen | ✓ | ✓ | — |
| Vergaderingen | ✓ | ✓ | ACL |
| Taken | ✓ | ✓ | ✓ |
| Factuur verzenden | ✓ | — | — |
| Afdracht Bonboys | ✓ | ✓ | — |
| Gebruikersbeheer & instellingen | ✓ | — | — |

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB (or SQLite for local dev)
- Cron (for scheduled task reminders)

## Installation

```bash
git clone https://github.com/gjhilderink/babb.git
cd babb
composer install
cp .env.example .env
php artisan key:generate
# Configure DB and mail in .env, then:
php artisan migrate --seed
php artisan serve
```

The seeder creates a default admin user — update the credentials in `database/seeders/DatabaseSeeder.php` before deploying.

### Scheduled tasks (server)

Add to crontab to enable daily reminders and other scheduled jobs:

```
* * * * * cd /path/to/babb && php artisan schedule:run >> /dev/null 2>&1
```

Run reminders manually:

```bash
php artisan tasks:send-reminders
```

## Tech stack

- Laravel 11 (minimal scaffold)
- Blade templates + Tailwind CSS (CDN, with explicit `<style>` block for custom brand colours)
- Alpine.js (CDN) — hamburger menu, dropdowns, reactive UI
- barryvdh/laravel-dompdf — PDF invoice export
- Role middleware (`role:admin`, `role:admin,bestuur`) registered in `bootstrap/app.php`
