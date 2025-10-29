# AI Agent Instructions — Crypto-Nest (concise)

This repository is a Laravel app (backend) with a Vite-powered frontend. The goal: give an AI coding agent the exact, actionable knowledge needed to be productive immediately.

Quick setup (Windows PowerShell)
```powershell
copy .env.example .env; php artisan key:generate; composer install; npm install
# create sqlite file if missing
if (-not (Test-Path database\database.sqlite)) { New-Item database\database.sqlite -ItemType File }
php artisan migrate --seed
php artisan serve
npm run dev
```

Where to read first
- `routes/web.php` — authoritative route layout and many inline validations (coin/symbol arrays). ALWAYS check this before changing public or wallet routes.
- `app/Services/TradeService.php` — pricing/trading simulation. Small code changes here change trading outcomes.
- `database/seeders/CurrencySeeder.php` — canonical seeded currencies and symbols.
- `app/Http/Middleware/SuperAdmin.php` and `app/Http/Controllers/Admin/*` — admin guard and high-privilege behavior.

Key repo-specific patterns
- Route-level symbol lists: many routes validate allowed coin symbols using local arrays (e.g., `/wallet/{type}`, `/coin/{symbol}`, `/forex/{symbol}`). These arrays are authoritative — add new symbols here and mirror changes in views and seeders.
- Duplicate symbol handling: symbol lists appear both in `routes/web.php` and some Blade views — update both or users will see 404s or UI mismatches.
- Admin vs user guards: two guards exist — default `auth` (users) and `auth:admin` (admins). Super-admin checks use `Auth::guard('admin')->user()->isSuperAdmin()`; role ids are relied on by code/seeders (common values: role_id=2 -> super-admin, role_id=1 -> regular admin).
- Admin wallet lookup: the `/wallet/{type}` route applies a specific lookup order (assigned admin -> super-admins -> role fallbacks) and uses UPPER(symbol) comparisons — copy this SQL/lookup when adding custodial-address logic.
- Static vs dynamic pages: many static pages use `Route::view()`; dynamic pages validate inputs in route closures and precompute props for the Blade views.

Assets & frontend
- Vite entry: `resources/js/app.js` and `resources/css` — built files land in `public/` and are referenced by Blade templates.
- Coin icons: `public/images/coins/` — add an appropriately named PNG/SVG when adding a currency.

Developer commands & checks
- Run tests: `php artisan test` (uses sqlite by default).
- Useful artisan commands: `php artisan route:list`, `php artisan tinker`, `php artisan migrate:status`.
- Logs: `storage/logs/laravel.log`.

Maintenance & scripts
- Utility scripts live in `scripts/` (wallet checks, trade inspectors). Read these for maintenance patterns and DB access examples.

PR checklist (concrete)
- If adding a coin/symbol:
  - Add symbol to the arrays in `routes/web.php` (wallet/trade/forex routes).
  - Add/adjust the Blade view under `resources/views/wallet/` if UI differs.
  - Add icon to `public/images/coins/`.
  - Update `database/seeders/CurrencySeeder.php` if you want it seeded.
  - Run `php artisan migrate --seed` (if applicable) and `php artisan test`.
- Admin features: use `auth:admin` routes and `->middleware('super-admin')` for high-privilege endpoints.

Gotchas & tips
- Do not assume a centralized symbol registry — `routes/web.php` is the master validator for several routes.
- Small changes in `app/Services/TradeService.php` can change simulated trading behavior—review tests and scripts when modifying.
- Role id conventions are used in code/seeders; changing them requires coordinated updates.

If something here is unclear or you want CI/linter rules added, tell me which area to expand and I will update this file.
