-- ============================================================================
-- PharmaLink Online Pharmacy Portal — Canonical Relational Schema (3NF)
-- Phase 04: Database Migration & Schema Design
-- Engine: InnoDB | Character Set: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `pharmalink`
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `pharmalink`;

SET FOREIGN_KEY_CHECKS = 0;

-- Drop recreated normalized tables to ensure clean 3NF type alignment across FK constraints
DROP TABLE IF EXISTS `order_status_history`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `prescriptions`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `inventory_batches`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `user_addresses`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `contact_tickets`;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `system_settings`;
-- Note: `users`, `members`, `contact`, and `delivery_registration` are preserved for backward compatibility!

-- ----------------------------------------------------------------------------
-- 1. Roles & Permissions (Enterprise RBAC)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `module` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT(11) NOT NULL,
  `permission_id` INT(11) NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Users (Enhanced with backward compatibility for legacy columns)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `role_id` INT(11) NOT NULL DEFAULT 7,
  `first_name` VARCHAR(100) NULL,
  `last_name` VARCHAR(100) NULL,
  `username` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NULL,
  `age` INT(11) NULL DEFAULT 18,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
  `status` ENUM('active', 'inactive', 'suspended', 'pending_verification') DEFAULT 'active',
  `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `failed_login_attempts` TINYINT UNSIGNED DEFAULT 0,
  `locked_until` DATETIME NULL,
  `password_reset_token` VARCHAR(64) NULL,
  `password_reset_expires` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role_id`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Customer Address Book
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `recipient_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `street_address` VARCHAR(255) NOT NULL,
  `apartment_unit` VARCHAR(100) NULL,
  `city` VARCHAR(100) NOT NULL,
  `state_province` VARCHAR(100) NOT NULL,
  `postal_code` VARCHAR(20) NOT NULL,
  `country` VARCHAR(100) DEFAULT 'United States',
  `is_default` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_addresses_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Product Categories
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `icon_class` VARCHAR(50) NULL,
  `image` VARCHAR(255) NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Products & Clinical Drug Master Catalog
-- (Recreate normalized structure replacing old empty table)
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `inventory_batches`;
DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) NOT NULL,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `generic_name` VARCHAR(255) NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `dosage_form` ENUM('tablet', 'capsule', 'syrup', 'inhaler', 'injection', 'cream', 'drops', 'powder') NOT NULL DEFAULT 'tablet',
  `strength` VARCHAR(100) NULL,
  `pack_size` VARCHAR(100) NOT NULL DEFAULT '1 Box',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cost_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `requires_prescription` BOOLEAN DEFAULT FALSE,
  `low_stock_threshold` INT UNSIGNED DEFAULT 10,
  `image` VARCHAR(255) NOT NULL DEFAULT 'panadol.jpg',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  INDEX `idx_products_category` (`category_id`),
  INDEX `idx_products_rx` (`requires_prescription`),
  INDEX `idx_products_active` (`is_active`),
  FULLTEXT `ft_products_search` (`name`, `generic_name`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Inventory Batches & FEFO Expiry Tracking
-- ----------------------------------------------------------------------------
CREATE TABLE `inventory_batches` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `batch_number` VARCHAR(100) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `allocated_quantity` INT NOT NULL DEFAULT 0,
  `manufacturing_date` DATE NOT NULL,
  `expiry_date` DATE NOT NULL,
  `supplier_name` VARCHAR(150) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  UNIQUE KEY `uk_product_batch` (`product_id`, `batch_number`),
  INDEX `idx_batches_expiry` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Legacy 'members' Table (Preserved for existing Admin Panel CRUD)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `itemname` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `itemdetail` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Prescriptions (Clinical Pharmacist Triage Queue)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `doctor_name` VARCHAR(150) NULL,
  `doctor_license` VARCHAR(100) NULL,
  `patient_name` VARCHAR(150) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_mime` VARCHAR(50) NOT NULL DEFAULT 'application/pdf',
  `file_size_bytes` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
  `reviewed_by` INT(11) NULL,
  `pharmacist_notes` TEXT NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_prescriptions_user` (`user_id`),
  INDEX `idx_prescriptions_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. Orders & Fulfillment Lifecycle
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(32) NOT NULL UNIQUE,
  `user_id` INT(11) NOT NULL,
  `shipping_address_id` INT(11) NULL,
  `prescription_id` INT(11) NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `order_status` ENUM('pending_payment', 'rx_review', 'processing', 'dispatched', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending_payment',
  `assigned_courier_id` INT(11) NULL,
  `delivery_notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`shipping_address_id`) REFERENCES `user_addresses` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_courier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_orders_user` (`user_id`),
  INDEX `idx_orders_status` (`order_status`),
  INDEX `idx_orders_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `batch_id` INT(11) NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `line_total` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`batch_id`) REFERENCES `inventory_batches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `from_status` VARCHAR(50) NOT NULL,
  `to_status` VARCHAR(50) NOT NULL,
  `comments` VARCHAR(255) NULL,
  `updated_by` INT(11) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. Payments (PCI-DSS SAQ-A Compliant Non-Sensitive Audit)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NULL,
  `card_type` VARCHAR(50) NOT NULL,
  `card_last4` VARCHAR(4) NOT NULL,
  `transaction_ref` VARCHAR(64) NOT NULL UNIQUE,
  `expiry_date` VARCHAR(10) NULL,
  `payment_status` VARCHAR(20) NOT NULL DEFAULT 'completed',
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_payments_order` (`order_id`),
  INDEX `idx_payments_ref` (`transaction_ref`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 11. Legacy 'delivery_registration' Table (Preserved for existing module)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `delivery_registration` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `mail` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(15) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `postal` VARCHAR(10) NOT NULL,
  `order_yes` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_delivery_mail` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 12. Legacy 'contact' Table (Preserved for existing module)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `topic` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contact_tickets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` VARCHAR(32) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 13. Customer Product Reviews & Verified Ratings
-- ----------------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `headline` VARCHAR(150) NOT NULL,
  `comment` TEXT NOT NULL,
  `is_verified_purchase` BOOLEAN DEFAULT TRUE,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_reviews_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 14. Immutable Security Audit Trail & System Configuration
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL,
  `entity_id` INT(11) NULL,
  `old_values` JSON NULL,
  `new_values` JSON NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_audit_user` (`user_id`),
  INDEX `idx_audit_action` (`action`),
  INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `system_settings` (
  `key_name` VARCHAR(100) PRIMARY KEY,
  `value_data` TEXT NOT NULL,
  `description` VARCHAR(255) NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
