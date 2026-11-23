# Project context

## Purpose and stack

Laravel 9 application using PHP 8, Blade, MySQL, session-based authentication, and the SB Admin 2 backend theme. The public site uses the existing frontend layout. The construction schema is documented in `mysql.sql`; see [database-context.md](database-context.md).

## Client flat purchase workflow

- Admin creates a project with a budget, manager/designer assignments, and flat rows. Each flat is stored in `flats` with its project and price.
- The assigned Project Manager or Designer can set `actual_end_date` once. After it is set, the completion date form and button are hidden and the server blocks repeat updates.
- A project can be offered for purchase only when it has a positive budget, at least one recorded project expense, and a completion date.
- An active role 8 client requests an available flat. `booking_requests` stores its details and review status. After Accountant approval, a linked confirmed record is created in `bookings`.
- Manager approval is limited to the assigned project and requires budget plus at least one expense. Accountant (role 7) alone verifies and confirms payment.
- The client must pay the flat's full listed price before the booking can be approved. No later/partial payment entry is offered. Approved payments are reported as income.
- The public status page searches by the client's phone and email. Client accounts see their own request status, confirmed flat, and payment information on `/client/dashboard`.
- Project expenses use the existing `expenses` table and cannot exceed project budget. Expense values are project-level, not assigned to individual flats.

## Current routes and code areas

- Public purchase and status pages: `FrontendController`, `resources/views/frontend/booking-request.blade.php`, and `resources/views/frontend/request-status.blade.php`.
- Project, flat, booking approval, expense, and finance report code: `Backend/Booking/BookingWorkflowController` and `resources/views/Backend/bookings/`.
- Client dashboard: `Backend/Client/ClientDashboardController` and `resources/views/Backend/client-dashboard.blade.php`.
- Backend controllers live under `app/Http/Controllers/Backend`; Eloquent models are grouped under `app/Models` by feature. Routes are in `routes/web.php`.
- User authentication uses the existing `users.password_hash` column through `User::getAuthPassword()`.

## Limits

Payment details are entered manually; there is no online payment gateway. The schema migration backfills legacy project flat listings and booking request rows, but the database connection has not been available to run it in this workspace.
