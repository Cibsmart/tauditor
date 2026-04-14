# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Laravel 8 application for processing and auditing payroll in Anambra State, Nigeria. Despite the directory name `anambra_tauditor`, `composer.json`/`README.md` identify this as the Anambra Payroll project; the current branch (`taudit_staging`) focuses on the audit side.

Stack: Laravel 8 (PHP ^8.0) · Inertia.js + Vue 2 · Tailwind CSS · MySQL/MariaDB (production) / SQLite in-memory (tests) · Redis + Laravel Horizon for queues · `maatwebsite/excel` for Excel I/O · `spatie/laravel-permission` for roles · `lorisleiva/laravel-actions`.

## Common commands

Backend:
- `composer install` — install PHP deps
- `php artisan serve` — run dev server
- `php artisan migrate` / `php artisan db:seed`
- `php artisan horizon` — run queue workers (Redis)
- `php artisan test` or `vendor/bin/phpunit` — run all tests
- `vendor/bin/phpunit --filter TestClassName` or `vendor/bin/phpunit tests/Unit/Foo/BarTest.php` — single test
- `vendor/bin/phpstan analyse` — static analysis (larastan, level 0, baseline in `phpstan-baseline.neon`)

Frontend (Laravel Mix / webpack):
- `npm ci` then `npm run dev` — build assets
- `npm run watch` — rebuild on change
- `npm run prod` — production build

Tests run against an in-memory SQLite DB (see `phpunit.xml`); no MySQL needed for the suite.

Default dev login: `john@payroll.com` / `password`.

## Architecture

The app is a classic Laravel + Inertia monolith, but the payroll/audit domain logic is organised into several non-standard top-level namespaces under `app/` that are worth knowing before editing:

- `app/Actions/` — single-purpose orchestrators built on `lorisleiva/laravel-actions` (e.g. `AuditPayScheduleAction`, `GenerateAutoPayScheduleAction`, `GenerateAndSendPayeData`). Controllers and jobs generally delegate into these rather than doing work inline. When adding a new end-to-end flow, prefer a new Action over fattening a controller.
- `app/Audit/` — pay-schedule auditors. `Analyse.php` is the entry point; individual `Check*` classes (`CheckBasicPay`, `CheckAllowances`, `CheckBankName`, `CheckAccountNumber`, `CheckNetPay`, `CheckNewBeneficiary`, …) each validate one field/rule on an imported schedule row. Bank-name exceptions (see recent commits) are handled in `CheckBankName` and also in `OtherScheduleImport`.
- `app/Compute/` — pure calculation helpers (`Tax`, `Prorate`) used by payroll generation.
- `app/Imports/` + `app/Exports/` — Maatwebsite Excel importers/exporters for the various schedule formats (main pay schedule, pension, leave, "other" schedule, MFB groupings, autopay variants). Exporters come in plain, group, and "other" variants that mirror the importers.
- `app/Models/` — Eloquent models including a `Meta` model with a `scopeIsActive` query scope (case-sensitivity there was recently fixed; keep scope names consistent with call sites).
- `app/ViewModels/` — view-model objects returned to Inertia pages, keeping controllers thin.
- `app/Jobs/` + Horizon — long-running work (audits, sending PAYE data, deduction confirmations) is dispatched to queues; `InitiateSendDeductionConfirmation` / `SendDeductionConfirmation` illustrate the initiate-then-fan-out pattern.
- `app/Http/` — standard Controllers / Requests / Resources / Middleware. Routes split across `routes/web.php` (Inertia pages) and `routes/api.php`.
- Frontend lives in `resources/js` (Vue 2 + Inertia pages) and `resources/views` (Blade shells); Ziggy exposes named routes to JS.

When touching a schedule flow, the typical call chain is: **Import (`app/Imports`) → Audit (`app/Audit/Analyse` + `Check*`) → Action (`app/Actions/*`) → Export (`app/Exports`) / Job dispatch**. Bug fixes in this area should usually land in the specific `Check*` class or the corresponding `*Import` — not in the controller.
