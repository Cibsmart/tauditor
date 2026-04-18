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
- `php artisan test` or `./vendor/bin/pest` — run all tests
- `./vendor/bin/pest --filter "test name"` or `./vendor/bin/pest tests/Unit/Foo/BarTest.php` — single test
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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v2
- laravel/framework (LARAVEL) - v12
- laravel/horizon (HORIZON) - v5
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- tightenco/ziggy (ZIGGY) - v2
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v12
- pestphp/pest (PEST) - v4
- tailwindcss (TAILWINDCSS) - v2
- vue (VUE) - v2

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `configuring-horizon` — Use this skill whenever the user mentions Horizon by name in a Laravel context. Covers the full Horizon lifecycle: installing Horizon (horizon:install, Sail setup), configuring config/horizon.php (supervisor blocks, queue assignments, balancing strategies, minProcesses/maxProcesses), fixing the dashboard (authorization via Gate::define viewHorizon, blank metrics, horizon:snapshot scheduling), and troubleshooting production issues (worker crashes, timeout chain ordering, LongWaitDetected notifications, waits config). Also covers job tagging and silencing. Do not use for generic Laravel queues without Horizon, SQS or database drivers, standalone Redis setup, Linux supervisord, Telescope, or job batching.
- `laravel-actions` — Build, refactor, and troubleshoot Laravel Actions using lorisleiva/laravel-actions. Use when implementing reusable action classes (object/controller/job/listener/command), converting service classes/controllers/jobs into actions, orchestrating workflows via faked actions, or debugging action entrypoints and wiring.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pest/core rules ===

# Pest

- This application uses Pest for testing. All tests must be written using Pest's `it()` / `test()` syntax. Use `php artisan make:test --pest {name}` to create a new test.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `./vendor/bin/pest --compact`.
- To run all tests in a file: `./vendor/bin/pest tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `./vendor/bin/pest --filter="test name"` (recommended after making a change to a related file).

</laravel-boost-guidelines>
