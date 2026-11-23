# Database context

## Schema source

`mysql.sql` is the corrected reference schema. Compare it with the migration history before changing persistence; it is not proof that every table is installed. The purchase workflow uses `flats`, `booking_requests`, `bookings`, and `client_payments`. Flat names and prices belong to a project through `flats.project_id`. `booking_requests` stores requests and their review statuses; `bookings` stores only confirmed flats and links back through `booking_request_id`.

The migration path creates both `booking_requests` and `bookings`. A later compatibility migration restores this two-table structure for databases that were partially changed to store request rows in `bookings`. A separate migration copies legacy `projects.available_units` lines into `flats` and removes that old column. Keep historical migrations intact so a fresh database can run the complete sequence.

`mysql.sql` does not define separate `staff` or `suppliers` tables. Employees with system accounts are represented by `users`; material orders do not store supplier references. Construction tables include roles, users, clients, projects, assignments, tasks, milestones, documents, project status history, materials, material orders, order items, site inventory, material usage, equipment, equipment deployments, labour, labour assignments, attendance, and expenses.

## Booking and payment rules

- `booking_requests.request_no` is an internal request reference; public status lookup uses client phone and email.
- A flat can be requested only for a completed project with positive budget and at least one expense. Manager review also checks budget and expense presence.
- Accountant (role ID 7) is the only role allowed to approve client payments. Full flat price is required for booking approval; later partial-payment entry is not part of the workflow.
- `client_payments.status` values are `pending` and `approved`. Only approved payments count as income.
- Booking `status=booked` marks a confirmed booking; request workflow status is stored in `booking_requests.status`.
- `expenses.category` is numeric and app code maps `1=Materials`, `2=Labor`, `3=Overhead`, `4=Other`.

## Schema cautions

- Each request/booking has its own `client_id`; project `client_id` is optional.
- Client login accounts use `users`; buyer contact records use `clients`, linked optionally by `clients.user_id`.
- Other numeric status codes in the supplied construction schema have no agreed meanings; do not infer them.
- Project and payment amounts use decimal columns; calculations should preserve cents.
