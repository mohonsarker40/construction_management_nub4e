CREATE TABLE `roles`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role_name` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NULL
);
ALTER TABLE
    `roles` ADD UNIQUE `roles_role_name_unique`(`role_name`);
CREATE TABLE `users`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `status` TINYINT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP());
ALTER TABLE
    `users` ADD UNIQUE `users_email_unique`(`email`);
CREATE TABLE `clients`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `client_name` VARCHAR(120) NOT NULL,
    `contact_person` VARCHAR(100) NULL,
    `email` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NOT NULL,
    `address` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP());
CREATE TABLE `staff`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `designation` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `specialization` VARCHAR(100) NULL,
    `daily_rate` DECIMAL(8, 2) NULL,
    `status` TINYINT NOT NULL DEFAULT 1
);
ALTER TABLE
    `staff` ADD UNIQUE `staff_email_unique`(`email`);
CREATE TABLE `projects`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT NOT NULL,
    `project_name` VARCHAR(150) NOT NULL,
    `project_type` VARCHAR(50) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `budget` DECIMAL(12, 2) NOT NULL,
    `start_date` DATE NOT NULL,
    `estimated_end_date` DATE NULL,
    `actual_end_date` DATE NULL,
    `status` TINYINT NOT NULL,
    `manager_staff_id` INT NULL,
    `created_by` INT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP());
CREATE TABLE `project_assignments`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `staff_id` INT NOT NULL,
    `project_role` VARCHAR(50) NOT NULL,
    `assigned_date` DATE NOT NULL,
    `end_date` DATE NULL
);
ALTER TABLE
    `project_assignments` ADD UNIQUE `project_assignments_project_id_staff_id_project_role_unique`(
        `project_id`,
        `staff_id`,
        `project_role`
    );
CREATE TABLE `tasks`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `assigned_to_staff_id` INT NULL,
    `start_date` DATE NULL,
    `due_date` DATE NULL,
    `status` TINYINT NOT NULL,
    `progress` DECIMAL(5, 2) NOT NULL,
    `created_by` INT NULL
);
CREATE TABLE `milestones`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `due_date` DATE NULL,
    `completed_date` DATE NULL,
    `status` TINYINT NOT NULL
);
CREATE TABLE `documents`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `document_type` VARCHAR(50) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `uploaded_by` INT NULL,
    `uploaded_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP());
CREATE TABLE `project_status_history`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `old_status` TINYINT NULL,
    `new_status` TINYINT NOT NULL,
    `changed_by` INT NULL,
    `changed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(), `remarks` VARCHAR(255) NULL);
CREATE TABLE `suppliers`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `supplier_name` VARCHAR(120) NOT NULL,
    `contact_person` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NULL,
    `city` VARCHAR(50) NULL,
    `address` VARCHAR(255) NULL,
    `gst_number` VARCHAR(50) NULL,
    `status` TINYINT NOT NULL DEFAULT 1
);
CREATE TABLE `materials`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `material_name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NULL,
    `unit_type` TINYINT NOT NULL,
    `default_unit_price` DECIMAL(10, 2) NULL,
    `reorder_level` DECIMAL(10, 2) NOT NULL
);
CREATE TABLE `material_orders`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `supplier_id` INT NOT NULL,
    `order_date` DATE NOT NULL,
    `expected_delivery_date` DATE NULL,
    `status` TINYINT NOT NULL,
    `total_amount` DECIMAL(12, 2) NOT NULL,
    `created_by` INT NULL
);
CREATE TABLE `order_items`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `material_id` INT NOT NULL,
    `quantity` DECIMAL(10, 2) NOT NULL,
    `unit_price` DECIMAL(10, 2) NOT NULL,
    `received_quantity` DECIMAL(10, 2) NOT NULL
);
CREATE TABLE `site_inventory`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `material_id` INT NOT NULL,
    `quantity_available` DECIMAL(10, 2) NOT NULL,
    `reorder_level` DECIMAL(10, 2) NOT NULL,
    `last_updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP());
ALTER TABLE
    `site_inventory` ADD UNIQUE `site_inventory_project_id_material_id_unique`(`project_id`, `material_id`);
ALTER TABLE
    `tasks` ADD CONSTRAINT `tasks_assigned_to_staff_id_foreign` FOREIGN KEY(`assigned_to_staff_id`) REFERENCES `staff`(`id`);
ALTER TABLE
    `projects` ADD CONSTRAINT `projects_manager_staff_id_foreign` FOREIGN KEY(`manager_staff_id`) REFERENCES `staff`(`id`);
ALTER TABLE
    `project_assignments` ADD CONSTRAINT `project_assignments_staff_id_foreign` FOREIGN KEY(`staff_id`) REFERENCES `staff`(`id`);
ALTER TABLE
    `tasks` ADD CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `order_items` ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY(`order_id`) REFERENCES `material_orders`(`id`);
ALTER TABLE
    `projects` ADD CONSTRAINT `projects_client_id_foreign` FOREIGN KEY(`client_id`) REFERENCES `clients`(`id`);
ALTER TABLE
    `tasks` ADD CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY(`created_by`) REFERENCES `users`(`id`);
ALTER TABLE
    `documents` ADD CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY(`uploaded_by`) REFERENCES `users`(`id`);
ALTER TABLE
    `milestones` ADD CONSTRAINT `milestones_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `projects` ADD CONSTRAINT `projects_created_by_foreign` FOREIGN KEY(`created_by`) REFERENCES `users`(`id`);
ALTER TABLE
    `project_status_history` ADD CONSTRAINT `project_status_history_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `documents` ADD CONSTRAINT `documents_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `material_orders` ADD CONSTRAINT `material_orders_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `order_items` ADD CONSTRAINT `order_items_material_id_foreign` FOREIGN KEY(`material_id`) REFERENCES `materials`(`id`);
ALTER TABLE
    `staff` ADD CONSTRAINT `staff_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `users` ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY(`role_id`) REFERENCES `roles`(`id`);
ALTER TABLE
    `site_inventory` ADD CONSTRAINT `site_inventory_material_id_foreign` FOREIGN KEY(`material_id`) REFERENCES `materials`(`id`);
ALTER TABLE
    `site_inventory` ADD CONSTRAINT `site_inventory_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `project_assignments` ADD CONSTRAINT `project_assignments_project_id_foreign` FOREIGN KEY(`project_id`) REFERENCES `projects`(`id`);
ALTER TABLE
    `project_status_history` ADD CONSTRAINT `project_status_history_changed_by_foreign` FOREIGN KEY(`changed_by`) REFERENCES `users`(`id`);
ALTER TABLE
    `material_orders` ADD CONSTRAINT `material_orders_created_by_foreign` FOREIGN KEY(`created_by`) REFERENCES `users`(`id`);
ALTER TABLE
    `material_orders` ADD CONSTRAINT `material_orders_supplier_id_foreign` FOREIGN KEY(`supplier_id`) REFERENCES `suppliers`(`id`);