-- PharmaLink Online Pharmacy Portal
-- Canonical Database Schema (PCI-DSS & Security Baseline Updated)
-- Database: pharmalink

CREATE DATABASE IF NOT EXISTS `pharmalink` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pharmalink`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `age` INT(11) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `contact`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `topic` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `delivery_registration`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `delivery_registration` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `mail` VARCHAR(255) NOT NULL,
  `telephone` VARCHAR(15) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `postal` VARCHAR(10) NOT NULL,
  `order_yes` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `payments` (PCI-DSS Compliant)
-- Raw PAN & CVV are NEVER stored. Only non-sensitive audit metadata.
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `card_type` VARCHAR(50) NOT NULL,
  `card_last4` VARCHAR(4) NOT NULL,
  `transaction_ref` VARCHAR(64) NOT NULL UNIQUE,
  `expiry_date` VARCHAR(10) NOT NULL,
  `payment_status` VARCHAR(20) NOT NULL DEFAULT 'completed',
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `members` (Pharmacy Inventory)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `itemname` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `itemdetail` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Seed Initial Sample Inventory Data (Scrubbed / Non-sensitive)
-- --------------------------------------------------------
INSERT INTO `members` (`itemname`, `type`, `quantity`, `itemdetail`, `image`) VALUES
('Panadol Extra', 'Common Things', 50, 'Paracetamol pain reliever and fever reducer', 'panadol.jpg'),
('Amoxicillin', 'Common Things', 30, 'Antibiotic for bacterial infections', 'amox.jpeg'),
('Atrolip', 'Common Things', 25, 'Cholesterol management medication', 'atrolip.jpeg'),
('Zin Tablet', 'Kids Items', 40, 'Nutritional zinc supplement', 'zin.jpeg'),
('Vitamin C', 'Women Items', 60, 'Immune booster chewable tablets', 'c.jpeg');

-- --------------------------------------------------------
-- Seed Initial Default Administrator Account
-- Credentials: admin@pharmalink.test / Admin@12345
-- --------------------------------------------------------
INSERT INTO `users` (`username`, `address`, `age`, `email`, `password`, `is_admin`) VALUES
('System Administrator', 'Headquarters, PharmaLink', 35, 'admin@pharmalink.test', '$2y$12$Z0g22jQ3qIq81mP5Q1wA4OG7sYwQ340tqB3fK3L/UqT7NfB8w9l7u', 1)
ON DUPLICATE KEY UPDATE `is_admin` = 1;
