# Project context

## Purpose and stack

Laravel 9 application using PHP 8, Blade, MySQL, session-based authentication, and the SB Admin 2 backend theme. The public site uses the existing frontend layout and homepage. Core construction CREATE statements are in `mysql.sql`; see [database-context.md](database-context.md).

## Client purchase workflow

- `GET /` shows projects on the public homepage. The public **Buy a flat** link opens `GET /booking-request`.
- A buyer first registers at `/client-register`. The account is created as role 8 with `status=0`, cannot log in yet, and waits for Admin approval in Client Management. Approval activates the User and creates/links the row in `clients`; only then can that buyer request a flat.
- Admin enters project flats on the existing `projects` row, one per line as `A-101 | 2500000`, and marks completion by setting `actual_end_date`. Only completed projects and their free flat cards appear in the purchase form. The server checks the selected project's completion, flat listing, price, and availability before accepting the request.
- A client submits contact details, selected project/flat, initial payment amount, payment method, and optional reference. The system creates/finds the client and saves a request with `pending_manager` status. It returns a request number that can be checked with the client's email at `/request-status`.
- A Project Manager (role ID 2) reviews requests only for projects assigned to their User account. Approval sends a request to the Accountant (`pending_accountant`); rejection closes the request.
- An Accountant (role ID 7) verifies and approves the initial payment. The system creates the booking and approved payment in one database transaction. The initial amount may be partial or equal to the full price.
- The Accountant can record later payments; they stay pending until approved. Only approved payments count as income or reduce the client's displayed balance.
- Project Managers and Accountants can record project expenses. Admin, Project Manager, and Accountant can open the financial report. A Client login (role ID 8) sees only matching client bookings and approved payments from **My Bookings & Payments**.
- Admin creates projects with an estimated finish date and assigns a Designer/Architect User. The assigned Designer records actual project expenses and sets the actual finish date. The project's recorded expenses are summed in the project screen and report; setting the finish date makes it eligible to appear for purchase.
- Project Manager and Designer accounts are User roles assigned directly to project rows. There is no separate Staff management screen/table in the reduced workflow.

## Current routes and code areas

- Public request and status pages: `FrontendController`, `resources/views/frontend/booking-request.blade.php`, and `resources/views/frontend/request-status.blade.php`.
- Review queue, payment entry/approval, project expenses, and report: `BookingWorkflowController` and `resources/views/Backend/bookings/`.
- Authenticated web routes are in `routes/web.php`; role checks are handled by the workflow controller. The existing `password_hash` mapping remains in `User::getAuthPassword()`.
- The sidebar links the existing role menus to booking requests, client bookings, and the separate financial report.

## Scope and limits

- Payment details are entered manually and verified by the Accountant. There is no online payment gateway or automatic bank confirmation.
- The GET finance report shows project income/expenses and client flat payment/balance summaries, with a print button. Expenses are not split among individual flats because the supplied expense schema is project-based.
- Other planned modules and placeholder navigation remain outside this semester workflow.

## Demo walkthrough

Run `php artisan db:seed` to create demo data without truncating existing roles or users. The seeded logins are `demo.admin@cms.test`, `demo.manager@cms.test`, `demo.designer@cms.test`, `demo.accountant@cms.test`, and `demo.client@cms.test`; each uses `demo12345`. The demo includes requests waiting at both review stages, one part-paid booking, one fully paid booking, a later pending payment, and project expenses.
