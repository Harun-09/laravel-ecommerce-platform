# NovaMart E-Commerce (Laravel 12)

A multi-role e-commerce platform built with Laravel 12 for customer, vendor, and admin workflows.

## Core Modules

- Customer storefront with product search, filters, quick view, cart, wishlist, checkout, order tracking, returns
- Vendor dashboard with product and order management plus report export
- Admin panel for users, vendors, categories, products, orders, returns, shipping, banners, reports, and audit logs
- Role and permission management using `spatie/laravel-permission`
- Stripe payment flow with webhook endpoint
- Invoice generation (A4 + thermal)
- Multi-language and currency preference support

## Tech Stack

- Backend: PHP 8.2+, Laravel 12
- Database: SQLite (default), MySQL compatible
- Frontend: Blade, Vite, Bootstrap-compatible assets
- Authorization: Spatie Laravel Permission
- PDF: `barryvdh/laravel-dompdf`

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Default Seed Users

All seeded users use password: `password`

- Super Admin: `superadmin@novamart.com`
- Admin: `admin@novamart.com`
- Vendor: `vendor1@novamart.com`
- Vendor: `vendor2@novamart.com`
- Vendor: `vendor3@novamart.com`
- Customer examples: `customer1@novamart.com` to `customer10@novamart.com`

## Useful Commands

```bash
php artisan test
php artisan migrate:fresh --seed
npm run dev
composer run dev
```

## Stripe Config

Set these values in `.env` before testing Stripe checkout:

- `STRIPE_MODE`
- `STRIPE_SANDBOX_PUBLIC_KEY`
- `STRIPE_SANDBOX_SECRET_KEY`
- `STRIPE_LIVE_PUBLIC_KEY`
- `STRIPE_LIVE_SECRET_KEY`
- `STRIPE_WEBHOOK_SECRET`

Webhook route:

- `POST /payment/stripe/webhook`

## Project Docs

- Proposal: `ecommerce_project_proposal.md`
- Workflow: `project_workflow.html`

## License

This project is released under the MIT License.
