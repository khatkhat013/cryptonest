## Crypto-Nest: quick AI agent guide (concise)

Purpose: give an AI coding agent the minimal, high-value context to be productive on this Laravel + Vite app.

Snapshot
- Stack: Laravel backend (routes → controllers → Eloquent models → Blade views). Frontend: Vite (entries in `resources/js/`, `resources/css/`), built assets in `public/`.
- Local DB: SQLite for dev (`database/database.sqlite`). Seeders under `database/seeders/`.

Read first (high signal)
- `routes/web.php` — authoritative: route layout, inline symbol validation arrays, and many closures that precompute view props.
- `app/Services/TradeService.php` — pricing/trade logic; impacts deterministic tests and simulations.
- `app/Http/Middleware/SuperAdmin.php` and `app/Http/Controllers/Admin/` — admin guard + privilege patterns.
- `database/seeders/CurrencySeeder.php` and `database/migrations/` — canonical currencies and schema.

Repo-specific, actionable patterns
- Symbol lists are validated inline in `routes/web.php`. When adding a coin (example: "doge"): 1) add symbol to arrays in `routes/web.php`; 2) add/adjust `resources/views/wallet/{type}.blade.php` or `Route::view()`; 3) add icon at `public/images/coins/{symbol}.png`; 4) update `CurrencySeeder.php` if seed data is needed.
- Wallet address selection lives in the `/wallet/{type}` route closure: it prefers assigned admin wallets, then role-based fallbacks (see the `AdminWallet` lookups and UPPER(symbol) checks in that closure).
- Auth: two guards — default `auth` (users) and `auth:admin` (admins). Super-admin checks use `Auth::guard('admin')->user()->isSuperAdmin()`.
- Static pages often use `Route::view()`; dynamic pages precompute props in route closures for Blade.

Dev workflows (Windows PowerShell)
1) Setup & run local dev servers:
```powershell
copy .env.example .env; php artisan key:generate; composer install; npm install
php artisan migrate --seed
php artisan serve
npm run dev
```
2) Tests: `php artisan test` (uses sqlite by default).
3) Useful commands: `php artisan route:list`, `php artisan tinker`, `php artisan migrate:status`.
4) Logs: `storage/logs/laravel.log`.

PR checklist (concrete)
- Adding a coin: update `routes/web.php` arrays → add view or `Route::view()` → add icon in `public/images/coins/` → update seeder → run `php artisan migrate --seed` and `php artisan test`.
- Admin features: use `auth:admin` and `->middleware('super-admin')` where needed; update role seeders when adding role ids.
- If changing `app/Services/TradeService.php`, run tests — changes are high-impact.

Where to expand
- If you want CI, linter, or composer/npm dev runner details added, tell me which area and I'll extend this file with commands and expected outputs.

Read these files for examples: `routes/web.php`, `app/Services/TradeService.php`, `app/Http/Middleware/SuperAdmin.php`, `app/Http/Controllers/Admin/`, `database/seeders/CurrencySeeder.php`, `resources/views/wallet/`, `public/images/coins/`.

If anything here is unclear or you want more detail in a specific area, tell me which part to expand.
# AI Agent Instructions for Crypto-Nest (concise)

This project is a small Laravel app with a Vite-powered frontend. The goal of this doc is to give an AI coding agent immediate, actionable knowledge to be productive.
Overview
- Backend: Laravel MVC (routes -> controllers -> Blade views). Look at `routes/web.php` to understand public vs authenticated and admin routes.
- Frontend: Vite + plain JS/CSS entry points in `resources/js/` and `resources/css/`. Built assets land in `public/`.
- DB: SQLite by default for local dev (`database/database.sqlite`). Seeders exist (`database/seeders/CurrencySeeder.php`).
Quick dev setup (what I run locally)
- Copy `.env.example` -> `.env` and run `php artisan key:generate`.
- Install PHP deps: `composer install`.
- Install JS deps: `npm install`.
- Dev servers: `php artisan serve` (backend) and `npm run dev` (Vite frontend with hot reload).
- To prepare DB: ensure `database/database.sqlite` exists, then run `php artisan migrate --seed`.
Key places and project-specific patterns
- `routes/web.php` is authoritative for route-level validation of coin/symbol lists. Many routes validate allowed symbols with local arrays and call `abort(404)` for unknown values (example: `/wallet/{type}`, `/coin/{symbol}`, `/forex/{symbol}`). When adding currencies or symbols, update these arrays.
- Admin area:
	- Controllers under `app/Http/Controllers/Admin/` (e.g., `AuthController`, `DashboardController`, `UserController`).
	- Admin routes are namespaced under `admin/*` and protected with `auth:admin` guard.
	- Super-admin checks are enforced by `app/Http/Middleware/SuperAdmin.php` (use `->middleware('super-admin')` where appropriate).
Views & UI:
	- Blade layout: `resources/views/layouts/app.blade.php`.
	- Per-coin pages live under `resources/views/wallet/` and some pages are created with `Route::view()` for static content.
	- Frontend JS: `resources/js/app.js` is the main Vite entry; static UI scripts live in `public/js/`.
Static assets:
	- Coin icons: `public/images/coins/` — add an icon when adding a new currency.
Data and seed conventions
- Seeded currencies are in `database/seeders/CurrencySeeder.php`. The DB schema is in `database/migrations/`.
- Admin registration forms create `AdminWallet` rows for provided `wallet_addresses` (see `app/Http/Controllers/Admin/AuthController.php`).
Developer workflows & debugging (concrete commands)
- Install & run (Windows PowerShell):
```powershell
cp .env.example .env; php artisan key:generate; composer install; npm install
php artisan migrate --seed
php artisan serve
npm run dev
```
- Run tests: `php artisan test` (uses the included sqlite DB by default).
- Useful artisan commands: `php artisan route:list`, `php artisan tinker`, `php artisan migrate:status`.
- Logs: check `storage/logs/laravel.log` for runtime errors.
## .github/copilot-instructions.md — Crypto-Nest (concise)

Purpose
- Give an AI coding agent the minimal, high-value context to be productive on this Laravel (v12) + Vite app.

Quick facts (read before coding)
- Start at `routes/web.php` — it is the authoritative place for route structure and inline symbol validation (many closures abort(404) for unknown symbols).
- Admin area is under `admin/*`, uses `auth:admin` and the `super-admin` middleware (`app/Http/Middleware/SuperAdmin.php`).
- Trade and pricing logic: `app/Services/TradeService.php` (high impact on tests and behavior).
- Seeded currencies and canonical symbols: `database/seeders/CurrencySeeder.php` and `database/migrations/`.

Dev setup (Windows PowerShell — copy+paste)
```powershell
copy .env.example .env; php artisan key:generate; composer install; npm install
php artisan migrate --seed
php artisan serve
npm run dev
```
Or run the combined dev script defined in `composer.json`:
```powershell
composer run dev
```

Project-specific patterns (be precise)
- Symbol lists are validated inline in `routes/web.php` (examples: `/wallet/{type}`, `/coin/{symbol}`, `/forex/{symbol}`). When adding a coin: 1) add symbol to the route arrays in `routes/web.php`; 2) add or update view under `resources/views/wallet/` (or `Route::view()`); 3) add icon to `public/images/coins/{symbol}.png`; 4) update `CurrencySeeder.php` if needed.
- Wallet address selection for `/wallet/{type}`: the route closure prefers an assigned admin, then super-admins (role_id=2), then regular admins (role_id=1) and matches currency via UPPER(symbol) checks — check the closure in `routes/web.php` for exact lookup order.
- Authentication: two guards — default `auth` (users) and `auth:admin` (admins). Use `Auth::guard('admin')->user()->isSuperAdmin()` pattern for super-admin checks.

Key files to read (in order)
- `routes/web.php`
- `app/Services/TradeService.php`
- `app/Http/Middleware/SuperAdmin.php`
- `app/Http/Controllers/Admin/` (AuthController, DashboardController)
- `database/seeders/CurrencySeeder.php`
- `resources/views/wallet/` and `public/images/coins/`

Quick PR checklist (concrete)
- Adding a coin: update `routes/web.php` arrays → add/update `resources/views/wallet/{type}.blade.php` or `Route::view()` → add `public/images/coins/{symbol}.png` → update seeder → run migrations and tests: `php artisan migrate --seed` && `php artisan test`.
- Admin routes/features: ensure `auth:admin` guard is applied and add `->middleware('super-admin')` for high-privilege endpoints.
- Changes to `app/Services/TradeService.php` are high-impact — run the full test suite.

If you want this file expanded (CI, linter rules, or common code-fix examples), tell me which area and I will extend it.
