# Project guidance

- This is a Laravel 9 application using PHP 8.0.2+, Blade, MySQL, and the SB Admin 2 admin theme.
- Read [docs/project-context.md](docs/project-context.md) for the current architecture and implementation status, and [docs/database-context.md](docs/database-context.md) before database work.
- Treat `mysql.sql` as the supplied reference schema, not as proof that all tables are installed or represented by migrations. Compare it with migrations before changing persistence.
- Follow existing Laravel conventions: web endpoints in `routes/web.php`, controllers under `app/Http/Controllers`, Eloquent models under `app/Models`, and Blade templates under `resources/views`.
- The application uses a nonstandard `users.password_hash` column; `User::getAuthPassword()` maps Laravel authentication to it. Preserve that mapping unless intentionally migrating authentication storage.
- Admin routes currently use the `authCheck` middleware alias and session authentication. There is no role-based authorization middleware yet.
- Keep schema enums/status codes explicit in code and documentation; do not infer numeric meanings unless they are defined in the schema, seeders, or implementation.
- Avoid treating placeholder sidebar links, dashboard figures, static frontend content, or unused CRUD stubs as implemented features.
- Do not edit generated/vendor files. Existing user changes should be preserved.

