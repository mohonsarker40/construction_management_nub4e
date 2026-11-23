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
CREATE TABLE `projects`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT NULL,
    `project_name` VARCHAR(150) NOT NULL,
    `project_type` VARCHAR(50) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `budget` DECIMAL(12, 2) NOT NULL,
    `start_date` DATE NOT NULL,
    `estimated_end_date` DATE NULL,
    `actual_end_date` DATE NULL,
    `available_units` TEXT NULL COMMENT 'One flat per line in label | price format',
    `status` TINYINT NOT NULL,
    `manager_user_id` INT NULL,
    `designer_user_id` INT NULL,
    `created_by` INT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP());
CREATE TABLE `project_assignments`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `user_id` INT NULL,
    `project_role` VARCHAR(50) NOT NULL,
    `assigned_date` DATE NOT NULL,
    `end_date` DATE NULL
);
ALTER TABLE
    `project_assignments` ADD UNIQUE `project_assignments_project_id_user_id_project_role_unique`(
        `project_id`,
        `user_id`,
        `project_role`
    );
CREATE TABLE `tasks`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `assigned_to_user_id` INT NULL,
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
    `projects` ADD CONSTRAINT `projects_manager_user_id_foreign` FOREIGN KEY(`manager_user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `projects` ADD CONSTRAINT `projects_designer_user_id_foreign` FOREIGN KEY(`designer_user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `project_assignments` ADD CONSTRAINT `project_assignments_user_id_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `tasks` ADD CONSTRAINT `tasks_assigned_to_user_id_foreign` FOREIGN KEY(`assigned_to_user_id`) REFERENCES `users`(`id`);
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

-- Additions from the supplied System Architecture CREATE statements.
ALTER TABLE `clients` ADD `user_id` INT NULL UNIQUE AFTER `id`;
ALTER TABLE `clients` ADD `status` TINYINT NOT NULL DEFAULT 1 COMMENT '0=Inactive, 1=Active';
ALTER TABLE `clients` ADD `updated_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `clients` ADD CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);
ALTER TABLE `projects` ADD `updated_at` TIMESTAMP NULL DEFAULT NULL;

CREATE TABLE `material_usage` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `project_id` INT NOT NULL, `material_id` INT NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL, `usage_date` DATE NOT NULL, `recorded_by` INT NULL, `remarks` VARCHAR(255) NULL,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`), FOREIGN KEY (`material_id`) REFERENCES `materials`(`id`),
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`)
);
CREATE TABLE `equipment` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `equipment_name` VARCHAR(100) NOT NULL, `model_number` VARCHAR(50) NULL,
  `daily_rate` DECIMAL(8,2) NOT NULL, `status` TINYINT NOT NULL DEFAULT 1, `purchase_date` DATE NULL
);
CREATE TABLE `equipment_deployments` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `equipment_id` INT NOT NULL, `project_id` INT NOT NULL,
  `start_date` DATE NOT NULL, `end_date` DATE NULL, `operator_user_id` INT NULL, `status` TINYINT NOT NULL DEFAULT 1,
  FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`), FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`),
  FOREIGN KEY (`operator_user_id`) REFERENCES `users`(`id`)
);
CREATE TABLE `labour` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `full_name` VARCHAR(100) NOT NULL, `phone` VARCHAR(20) NULL,
  `skill_type` VARCHAR(50) NOT NULL, `daily_wage` DECIMAL(8,2) NOT NULL, `status` TINYINT NOT NULL DEFAULT 1
);
CREATE TABLE `labour_assignments` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `project_id` INT NOT NULL, `labour_id` INT NOT NULL,
  `start_date` DATE NOT NULL, `end_date` DATE NULL, `daily_wage` DECIMAL(8,2) NOT NULL, `status` TINYINT NOT NULL DEFAULT 1,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`), FOREIGN KEY (`labour_id`) REFERENCES `labour`(`id`)
);
CREATE TABLE `attendance` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `labour_id` INT NOT NULL, `project_id` INT NOT NULL,
  `attendance_date` DATE NOT NULL, `status` TINYINT NOT NULL, `overtime_hours` DECIMAL(4,2) NOT NULL DEFAULT 0,
  `recorded_by` INT NULL, UNIQUE KEY `attendance_labour_project_date_unique` (`labour_id`, `project_id`, `attendance_date`),
  FOREIGN KEY (`labour_id`) REFERENCES `labour`(`id`), FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`),
  FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`)
);
CREATE TABLE `expenses` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `project_id` INT NOT NULL,
  `category` TINYINT NOT NULL COMMENT '1=materials, 2=labor, 3=overhead, 4=other',
  `amount` DECIMAL(12,2) NOT NULL, `expense_date` DATE NOT NULL, `description` VARCHAR(255) NULL,
  `recorded_by` INT NULL, `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`), FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`)
);

-- Client-to-accounting purchase workflow additions.
CREATE TABLE `booking_requests` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `request_no` VARCHAR(24) NOT NULL UNIQUE,
  `project_id` INT NOT NULL, `client_id` INT NOT NULL, `unit_label` VARCHAR(50) NOT NULL,
  `agreed_amount` DECIMAL(12,2) NOT NULL, `initial_payment_amount` DECIMAL(12,2) NOT NULL,
  `payment_method` VARCHAR(30) NOT NULL, `payment_reference` VARCHAR(100) NULL, `client_message` TEXT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'pending_manager' COMMENT 'pending_manager, pending_accountant, approved, rejected',
  `manager_reviewed_by` INT NULL, `manager_reviewed_at` TIMESTAMP NULL, `accountant_reviewed_by` INT NULL,
  `accountant_reviewed_at` TIMESTAMP NULL, `rejection_note` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `booking_requests_project_unit_status_index` (`project_id`, `unit_label`, `status`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`), FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`),
  FOREIGN KEY (`manager_reviewed_by`) REFERENCES `users`(`id`), FOREIGN KEY (`accountant_reviewed_by`) REFERENCES `users`(`id`)
);
CREATE TABLE `bookings` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `booking_request_id` INT NOT NULL UNIQUE,
  `project_id` INT NOT NULL, `client_id` INT NOT NULL, `unit_label` VARCHAR(50) NOT NULL,
  `agreed_amount` DECIMAL(12,2) NOT NULL, `booking_date` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'booked' COMMENT 'booked=confirmed booking', `created_by` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `bookings_project_unit_unique` (`project_id`, `unit_label`),
  FOREIGN KEY (`booking_request_id`) REFERENCES `booking_requests`(`id`), FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`),
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`), FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
);
CREATE TABLE `client_payments` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `booking_id` INT NOT NULL, `amount` DECIMAL(12,2) NOT NULL,
  `payment_date` DATE NOT NULL, `payment_method` VARCHAR(30) NOT NULL, `reference_no` VARCHAR(100) NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending=awaiting approval, approved=counts as revenue',
  `recorded_by` INT NOT NULL, `approved_by` INT NULL, `approved_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`), FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`),
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`)
);
