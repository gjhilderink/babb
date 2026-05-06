# BABB Portaal

Business club administration portal built with Laravel 11.

## Features

- **Leden** — manage members with membership types, contact details, and status tracking
- **Producten** — product/service catalog with pricing and VAT rates
- **Facturen** — create, send, and track membership invoices with PDF export

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

## Tech stack

- Laravel 11
- Blade templates + Tailwind CSS (CDN)
- Alpine.js (CDN)
- barryvdh/laravel-dompdf for PDF invoices
