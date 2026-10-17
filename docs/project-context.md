# Project context

## Purpose and stack

The repository is a Laravel 9/PHP 8 project intended to grow into a construction management system. It uses Blade templates, Laravel's session-based web authentication, Eloquent, MySQL by default, Sanctum for the stock authenticated API user endpoint, and Vite for frontend asset builds. The admin UI is based on SB Admin 2. The public landing page uses a separate news-style template and static/local frontend assets.

Dependencies and common commands are defined in `composer.json` and `package.json`. The README describes Composer installation, `.env` setup, key generation, and migrations. No project-specific feature test coverage is present; current tests are Laravel example stubs.

## Current request flow

- `GET /` → `FrontendController@index` → `resources/views/homepage.blade.php`.
- `GET /login` renders `Backend/Auth/login.blade.php`; `POST /login` validates email/password and calls Laravel `Auth::attempt()`.
- Successful login regenerates the session and redirects to the named `dashboard` route. Failed login returns to the form with a `failed` flash message.
- `/admin` routes are grouped behind the custom `authCheck` middleware. `/admin/` redirects to the dashboard; `/admin/dashboard` renders a static dashboard; `POST /admin/logout` logs out.
- `routes/api.php` only exposes Laravel's default `GET /api/user` under `auth:sanctum`.
- The admin sidebar contains many placeholder `href="#"` entries for features that do not yet have routes or controllers.

## Main code areas

- `routes/`: web, API, broadcast-channel, and console routes.
- `app/Http/Controllers/`: public landing controller and backend login/dashboard/role controllers. Most resource methods are empty stubs.
- `app/Models/`: `User` and `Role` match the authentication/authorization starter domain. Other models (`BlogPost`, `Category`, `Comment`, `Frontend`, `GeneralSetting`, `Login`, `Subscriber`) look like legacy/news-site leftovers and are not wired to current routes.
- `resources/views/Backend/`: login, static dashboard, shared admin layout/topbar/sidebar, and a standalone template page (`tables.blade.php`).
- `resources/views/frontend/` and `homepage.blade.php`: public news-template views. Current content is mostly static or placeholder content.
- `database/`: two migrations (users and roles), initial seeders, and a default factory.
- `public/Backend/`: SB Admin 2 CSS, JS, SCSS, and images. Frontend assets are referenced from `public/frontend/`.

## Authentication and access notes

`User::getAuthPassword()` maps Laravel's expected password field to `password_hash`. Seeded passwords are hashed with Laravel's `Hash::make()`. `AuthCheckMiddleware` checks only whether a user is logged in; it does not check their role, status, or permissions. Eight role names are seeded, but no role-based route or feature authorization is implemented. The separate `AuthMiddlewareCheck` class is currently unused.

## Known implementation gaps

- Dashboard metrics and charts are hardcoded template/demo values, not database aggregates.
- Role CRUD methods are empty; project, client, staff, task, milestone, document, supplier, purchasing, inventory, reporting, and transaction workflows do not have corresponding routes/controllers in the current app.
- The admin navigation reflects planned modules, not functional screens.
- The current migrations do not create the full `mysql.sql` schema; only users and roles migrations exist, and they do not fully match the SQL definition. See [database-context.md](database-context.md).
- Legacy content models refer to tables/columns absent from `mysql.sql`; do not assume those tables exist.
- The README's `php artisan migrate` setup instructions only create the tables represented by the migrations.

## Change orientation

Before implementing a feature, establish whether it belongs to the construction domain in `mysql.sql` or the legacy news-template remnants. For a database feature, inspect all three sources: `mysql.sql`, migrations, and the relevant model/controller. Add migrations and relationships deliberately; do not rely on the SQL dump having been applied. Preserve the existing `password_hash` authentication convention unless changing it is part of the task.

