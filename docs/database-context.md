# Database context

## Schema source

The user's **System Architecture & Database Specifications** CREATE TABLE statements are the construction schema source. `mysql.sql` contains those tables, with invalid duplicate keys and foreign keys from the draft corrected. The purchase process reuses `booking_requests`, `bookings`, and `client_payments`; it does not add a separate client-purchase workflow table. `projects.available_units` stores each flat number and price as one `label | price` line, and `projects.actual_end_date` marks project completion for client sales. Nullable unique `clients.user_id` links client logins to their business record.

`mysql.sql` no longer has separate `staff` or `suppliers` tables. Employees with system accounts are represented by `users`; project manager, assignment, task, and equipment operator relationships point to Users. Material orders no longer store supplier references. Other construction tables remain: `roles`, `users`, `clients`, `projects`, `project_assignments`, `tasks`, `milestones`, `documents`, `project_status_history`, `materials`, `material_orders`, `order_items`, `site_inventory`, `material_usage`, `equipment`, `equipment_deployments`, `labour`, `labour_assignments`, `attendance`, and `expenses`. The booking workflow uses three tables and a few columns on existing tables.

The project, booking request, booking, client payment, and expense tables each have a separate migration after the existing `users` and `clients` migrations. Use this migration path or import the complete `mysql.sql` schema; do not run both against the same database because they define the same tables. The migration set does not contain every construction table from the full SQL schema.

## Workflow relationships and statuses

- `booking_requests` links a client and project to a requested flat/unit, agreed price, claimed initial payment, and review history.
- Public client registration creates a `users` row as role 8 and inactive status (`0`). Admin approval activates it and creates/links the matching `clients` row.
- Projects store `manager_user_id`, `designer_user_id`, estimated/actual end dates, and listed flat prices. Designer expense entries use the existing `expenses` table; recorded project expenses are summed for display/reporting.
- Only completed projects (`actual_end_date` is set) with available listed flats appear for purchase. A flat is unavailable while booked or awaiting review.
- Request statuses are explicit: `pending_manager`, `pending_accountant`, `approved`, and `rejected`.
- Accountant approval creates one `bookings` row and its initial approved `client_payments` row together.
- `client_payments.status`: `pending` waits for Accountant approval; `approved` is included in revenue and client paid totals.
- Booking status `booked` means the confirmed booking created after approval.
- The supplied `expenses.category` field is numeric. This app explicitly maps `1=Materials`, `2=Labor`, `3=Overhead`, `4=Other`; this mapping is not inferred from the original draft.
- Project Manager (role ID 2) access is limited to projects assigned directly to their User account. Accountant (role ID 7) processes payments. Client (role ID 8) sees bookings linked to the same client account/email.

## Schema cautions

- A project can have no primary client. Each purchase request and confirmed booking stores its own `client_id`, so multiple buyers can purchase different units in the same project.
- `users` stores login credentials and role; `clients` stores buyer/contact information linked to bookings. Frontend booking currently creates or finds a Client record; it does not create a password/login account.
- Project Manager is a User role, not a separate Staff record. Client remains a separate buyer profile linked optionally to a User login.
- Supplier records and the supplier link on material orders are removed.
- Other status and unit codes in the supplied schema have no agreed numeric meanings; do not infer them.
- Projects, expenses, and purchases use `DECIMAL` amounts. Keep validation and calculations in decimal form.
