# Dream Travel and Tours System

A separate copy of the safari backend, reorganized into the feature-module layout used by `tzrestaurantsystem`, with a Vue 3 / TypeScript administration workspace using that project's Tailwind/Reka component library and theme.

## Run the supplied local copy

PHP 8.2–8.4, Composer 2, Node 22 and npm are supported by the supplied dependency locks. The existing local installation uses a separate seeded SQLite database. Git excludes dependencies, local `.env` files, application keys, databases, logs, and caches. For a new clone, follow the setup section below first.

```bash
cd /mnt/CE8007F58007E337/D/B_PROJECTS/dreamtoursystem
./start.sh
```

Open **http://127.0.0.1:5174**. The backend runs at **http://127.0.0.1:8001**. These ports let the original project continue using 8000/5173.

The launcher checks the configured database driver and selects an installed PHP runtime that supports it. It prints the selected runtime before starting. To choose one explicitly, use `PHP_BIN=/usr/bin/php8.3 ./start.sh`. If login reports "could not find driver", stop the old server and restart with this launcher; no database reset is needed. Use the same PHP executable for Artisan commands and queue workers.

Local seeded administrator:

- Email: `admin@serenbluesafaris.com`
- Password: `1234567890`

For queued account emails, open another terminal:

```bash
cd BACKEND
php artisan queue:work
```

Email uses the log driver. Quotation PDFs use DomPDF by default; Browsershot remains supported with Node/Puppeteer/Chrome configured.

## Structure

```text
BACKEND/
  app/Project/
    Auth/                         # Existing authentication, API and web routes
    Modules/
      Core/Users/                 # User, SystemUser, controller, services, requests, seeders
      Core/Roles/
      Core/Permissions/
      System/Trips/               # Trip models, planner, services, requests, migrations
      System/Quotations/
      System/Bookings/
      System/Destinations/
      ...
    Workspace/                    # Session-based Vue admin endpoints and form descriptors
    _Src/                         # Module registry and application service provider
  database/seeders/DatabaseSeeder.php
  resources/views/                # Preserved document, email and management templates
FRONTEND/
  src/pages/Auth/
  src/pages/modules/core/
  src/pages/modules/system/
  src/layouts/
  src/components/ui/              # Shared components from tzrestaurantsystem
  src/components/workspace/       # Shared records/detail/form components
  src/modules.json
```

Laravel stays on the existing locked **11.51.0** version. This is an architecture migration, not a Laravel 12 upgrade.

## What is migrated

- 410 existing PHP classes moved into 53 registered feature/infrastructure modules.
- All 103 existing migrations are discovered from their module folders in their original timestamp order.
- Model relationships, validation, business actions, observers and the original public API paths remain available.
- Existing polymorphic model names are mapped to the new namespaces for compatibility with stored attachment, role and activity records.
- Public API route names are namespaced with `api.` to avoid collisions with management route names. URLs are unchanged.
- Vue login, dashboard, responsive sidebar, light/dark theme, and 34 module list/detail screens.
- Native Vue dialogs submit the original backend forms, with their existing field names, lookup options, CSRF protection and validation.

## UI migration boundary

The backend module reorganization is complete. The Vue workspace is an initial interface migration, **not a complete conversion of every Blade workflow**.

The workspace derives form descriptors from the preserved management templates. It does not execute their JavaScript. Forms that depend on custom Blade JavaScript, dynamic dependent selections, inline edit modals, the trip itinerary planner, quotation builder, media library and specialised reporting remain available in the original management screens through **Full management**. Blade still renders guest itineraries, PDFs and email templates.

The new workspace currently requires an active `SystemUser` with the `SuperAdmin` role. The existing role/permission system and original management routes are retained. Additional staff-role access should be mapped explicitly before opening the new workspace to those roles.

Some source controllers (Blogs, Pages, Refunds) had no implemented management screen. The workspace provides read-only lists/details for these; it does not invent missing create/update workflows.

Current list endpoints preserve the source application's load-all behaviour, with search and pagination in Vue. For large installations, add server-side pagination in each module's list service.

## Fresh checkout or MySQL setup

.env files stay on your machine and are not included in Git. The existing local `.env` selects SQLite. `.env.example` shows the original MySQL option with a separate database name and blank payment credentials. Never reuse the original project's database for initial seeding.

```bash
git clone https://github.com/peternakei/dreamtoursystem.git dreamtoursystem
cd dreamtoursystem/BACKEND
composer install
cp .env.example .env
# Configure your new local MySQL database and credentials in .env first.
php artisan key:generate
php artisan migrate --seed
cd ../FRONTEND
npm ci
cp .env.example .env
```

For SQLite on a fresh checkout, create `BACKEND/database/database.sqlite` and set `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite` instead. Run the seeders once on the empty database. Avoid `migrate:fresh` on databases with data to keep.

The copied `public/storage` directory contains sample media and is used by the attachment disk. Do not replace it blindly with `storage:link`. Optional source-image reconversion still needs `sharp` and the source image library; existing converted fixtures work without it.

The frontend dev server proxies `/backend` to Laravel. Production hosting must provide the equivalent same-origin proxy, SPA history fallback, and serve Laravel from `BACKEND/public`.

## Checks

```bash
cd BACKEND
php artisan test
cd ../FRONTEND
npm run typecheck
npm run build
```

Tests use a separate in-memory SQLite database. See `FRONTEND/docs/MIGRATION.md` for migration notes and verification evidence.
