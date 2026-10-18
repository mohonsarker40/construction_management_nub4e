# Database context

## Source and status

`mysql.sql` is the supplied SQL schema for the intended construction-management domain. It defines 15 tables: `roles`, `users`, `clients`, `staff`, `projects`, `project_assignments`, `tasks`, `milestones`, `documents`, `project_status_history`, `suppliers`, `materials`, `material_orders`, `order_items`, and `site_inventory`. The SQL includes several unique constraints and foreign keys. It is not reproduced by the current Laravel migrations.

The repository currently has only two migrations:

- `create_users_table`: `id`, `role_id`, name/email/password_hash/phone/status, and Laravel timestamps; `email` unique.
- `create_roles_table`: `id`, `role_name`, `description`, and Laravel timestamps.

The migrations are inconsistent with `mysql.sql`: migration `users.role_id` is an integer but has no FK; schema SQL adds a role FK. SQL `roles.role_name` is unique and `description` nullable with no timestamps, while migration role name is not unique, description is non-null, and timestamps are present. SQL `users.created_at` exists without `updated_at`; migration creates both timestamps. Reconcile intentionally before treating either definition as canonical for a live database.

## Domain map

- **Identity/access:** `roles` → `users`; users can optionally map to one `staff` record.
- **Customers/projects:** `clients` → `projects`; projects can have a manager (`staff`) and creator (`users`).
- **Delivery:** `projects` → `project_assignments`, `tasks`, `milestones`, `documents`, and `project_status_history`. Assignments connect staff to projects and have a project role and assignment dates.
- **Procurement/inventory:** `suppliers` → `material_orders` → `order_items` → `materials`; `site_inventory` tracks a material quantity per project.

SQL foreign-key links:

- `users.role_id` → `roles.id`; `staff.user_id` → `users.id`.
- `projects.client_id` → `clients.id`; `projects.manager_staff_id` → `staff.id`; `projects.created_by` → `users.id`.
- `project_assignments.project_id` → `projects.id`; `project_assignments.staff_id` → `staff.id`.
- `tasks.project_id` → `projects.id`; `tasks.assigned_to_staff_id` → `staff.id`; `tasks.created_by` → `users.id`.
- `milestones.project_id` → `projects.id`.
- `documents.project_id` → `projects.id`; `documents.uploaded_by` → `users.id`.
- `project_status_history.project_id` → `projects.id`; `changed_by` → `users.id`.
- `material_orders.project_id` → `projects.id`; `supplier_id` → `suppliers.id`; `created_by` → `users.id`.
- `order_items.order_id` → `material_orders.id`; `material_id` → `materials.id`.
- `site_inventory.project_id` → `projects.id`; `material_id` → `materials.id`.

Unique constraints in SQL: role name, user email, staff email, the combination `(project_id, staff_id, project_role)` in assignments, and `(project_id, material_id)` in site inventory.

## Status and numeric fields

Several columns use `TINYINT` (`users.status`, `projects.status`, task/milestone/order statuses, `materials.unit_type`, and history old/new status). Only user status semantics are documented in the checked-in migration (`0=Inactive, 1=Active`). Seeded users use status `1`. Do not assume the other numeric codes without an agreed mapping. Decimal quantities, progress, rates, and money use `DECIMAL`; preserve decimal precision in validation and calculations.

## Application mapping caveats

- `User` uses `password_hash` through `getAuthPassword()` and belongs to `Role`.
- There are no Eloquent models yet for the core construction entities. Existing models for blog/news content (`BlogPost`, `Category`, `Comment`, and others) do not match the supplied SQL schema and likely refer to another project/domain.
- SQL does not declare all possible relationships as database constraints: for example, `project_assignments` uniqueness is composite, but no cascade behavior is specified. Do not add cascade deletes or extra constraints without deciding the intended lifecycle.
- Keep this table list aligned with the `CREATE TABLE` statements when the supplied SQL changes.

