-- ============================================================================
-- PharmaLink Online Pharmacy Portal — Deterministic Production Seeds
-- Phase 04: Database Migration & Seeding
-- Engine: InnoDB | Character Set: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

USE `pharmalink`;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. Seed Roles (Enterprise 8-Role RBAC Model)
-- ----------------------------------------------------------------------------
INSERT INTO `roles` (`id`, `slug`, `name`, `description`) VALUES
(1, 'super_admin',       'Super Administrator', 'Complete system authority, global configuration & security governance'),
(2, 'pharmacy_owner',    'Pharmacy Owner',      'Commercial revenue oversight, procurement, financial analytics'),
(3, 'branch_manager',    'Branch Manager',      'Operational order dispatch, staff supervision, and customer escalations'),
(4, 'pharmacist',        'Licensed Pharmacist', 'Clinical prescription verification, contraindication review, drug dispensing'),
(5, 'inventory_manager', 'Inventory Manager',   'Stock lot batches, supplier intake, FEFO management, and waste audits'),
(6, 'delivery_staff',    'Delivery Courier',    'Logistics fulfillment, recipient signature capture, dispatch tracking'),
(7, 'customer',          'Registered Patient',  'Consumer healthcare purchasing, prescription uploads, order tracking'),
(8, 'guest',             'Storefront Visitor',  'Public catalog browsing, disease knowledgebase, unauthenticated inquiry')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`);

-- ----------------------------------------------------------------------------
-- 2. Seed Permissions
-- ----------------------------------------------------------------------------
INSERT INTO `permissions` (`id`, `slug`, `name`, `module`) VALUES
(1,  'catalog.view',            'View Product Catalog',             'catalog'),
(2,  'catalog.manage',          'Create/Edit/Delete Products',      'catalog'),
(3,  'inventory.view',          'View Stock & Batches',             'inventory'),
(4,  'inventory.manage',        'Adjust Stock & Intake Batches',    'inventory'),
(5,  'prescriptions.upload',    'Upload Personal Prescriptions',    'prescriptions'),
(6,  'prescriptions.review',    'Clinical Review & Approval',       'prescriptions'),
(7,  'orders.create',           'Place Personal Orders',            'orders'),
(8,  'orders.view_all',         'View All Branch Orders',           'orders'),
(9,  'orders.dispatch',         'Assign Couriers & Update Dispatch','orders'),
(10, 'orders.deliver',          'Confirm Order Delivery',           'orders'),
(11, 'reports.revenue',         'View Sales & Financial Analytics', 'reports'),
(12, 'reports.clinical',        'View Drug Dispensation Audits',    'reports'),
(13, 'users.manage',            'Manage Staff & User Accounts',     'users'),
(14, 'audit.view',              'View Security Audit Trail',        'security'),
(15, 'settings.manage',         'Modify Store Configuration',       'settings')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ----------------------------------------------------------------------------
-- 3. Map Role Permissions
-- ----------------------------------------------------------------------------
DELETE FROM `role_permissions`;

-- Super Admin: All Permissions (1-15)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions`;

-- Pharmacy Owner: Commercial, Inventory, Reports, Orders, Users
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1), (2, 2), (2, 3), (2, 4), (2, 8), (2, 9), (2, 11), (2, 12), (2, 13), (2, 14);

-- Branch Manager: Operations, Inventory, Dispatch, Orders
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1), (3, 2), (3, 3), (3, 4), (3, 8), (3, 9), (3, 11), (3, 13);

-- Pharmacist: Clinical Review, Catalog View, Clinical Reports
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4, 1), (4, 3), (4, 6), (4, 8), (4, 12);

-- Inventory Manager: Stock & Batches, Catalog
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(5, 1), (5, 3), (5, 4);

-- Delivery Staff: View Assigned Dispatches & Confirm Delivery
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(6, 10);

-- Customer: Browse, Upload Rx, Create Orders
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(7, 1), (7, 5), (7, 7);

-- Guest: Browse Catalog
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(8, 1);

-- ----------------------------------------------------------------------------
-- 4. Seed Demo Accounts for All Roles (Passwords Bcrypt-Hashed, Cost 12)
-- ----------------------------------------------------------------------------
-- Password Reference:
--   Super Admin:       admin@pharmalink.test      / Admin@12345
--   Pharmacy Owner:    owner@pharmalink.test      / Owner@12345
--   Branch Manager:    manager@pharmalink.test    / Manager@12345
--   Pharmacist:        pharmacist@pharmalink.test / Pharma@12345
--   Inventory Manager: inventory@pharmalink.test  / Stock@12345
--   Delivery Staff:    courier@pharmalink.test    / Courier@12345
--   Customer:          patient@pharmalink.test    / Patient@12345
-- ----------------------------------------------------------------------------
DELETE FROM `users` WHERE `id` <= 7 OR `email` LIKE '%@pharmalink.test';
INSERT INTO `users` (`id`, `role_id`, `first_name`, `last_name`, `username`, `address`, `age`, `email`, `password`, `phone`, `status`, `is_admin`) VALUES
(1, 1, 'System', 'Administrator', 'admin', 'HQ Medical Center, Suite 100', 38, 'admin@pharmalink.test', '$2y$12$CP8RiRA6jkPZHJ5O0pUtgOkjQK74gCKKJ8b1pCTcOWj3dQ4oqyYU.', '+1 (555) 019-2831', 'active', 1),
(2, 2, 'Elena', 'Vance', 'owner', '45 Executive Boulevard, New York', 46, 'owner@pharmalink.test', '$2y$12$rWK1dpFyXU0/DrvA9E4wOOIuN6Lcv2f1UjS3pESNJZMsyuNDVSvVe', '+1 (555) 019-2832', 'active', 1),
(3, 3, 'David', 'Sterling', 'manager', '12 Central Dispensary Ave, Boston', 41, 'manager@pharmalink.test', '$2y$12$ZSTP8S0czCMK4G4ESM6XKOOAgk2t2tXwOLkWFGr0aTb3FVN6g5aCy', '+1 (555) 019-2833', 'active', 1),
(4, 4, 'Dr. Sarah', 'Kaufman', 'pharmacist', 'Clinical Care Tower 3, Cambridge', 35, 'pharmacist@pharmalink.test', '$2y$12$gM/c9rG/8.iEVSm5OnWokuQnkp/o3IKPhbJDCyS3W.XFU6rb7NAg2', '+1 (555) 019-2834', 'active', 0),
(5, 5, 'Marcus', 'Chen', 'inventory', 'Warehouse Logistics Hub B, Newark', 32, 'inventory@pharmalink.test', '$2y$12$lY4Wnx4hQ4QzYf.JsA2I9OO3XJuVGLDK8EdQb4V9dPqeddnqL4QrK', '+1 (555) 019-2835', 'active', 0),
(6, 6, 'Leo', 'Rodriguez', 'courier', 'Dispatch Station 4, Jersey City', 27, 'courier@pharmalink.test', '$2y$12$sTFYhzagQfdIteFofyKB2O5b.qCjX1Z5qRmHjOFI20mfK16EYyLkq', '+1 (555) 019-2836', 'active', 0),
(7, 7, 'Martha', 'Jenkins', 'patient', '742 Evergreen Terrace, Springfield', 62, 'patient@pharmalink.test', '$2y$12$GurXVVv9lssq3JDccGxT3OUqgQpABUbh1P/3e4puWS9hPo8uP8SkW', '+1 (555) 019-2837', 'active', 0)
ON DUPLICATE KEY UPDATE 
  `role_id` = VALUES(`role_id`),
  `first_name` = VALUES(`first_name`),
  `last_name` = VALUES(`last_name`),
  `username` = VALUES(`username`),
  `address` = VALUES(`address`),
  `age` = VALUES(`age`),
  `email` = VALUES(`email`),
  `password` = VALUES(`password`),
  `phone` = VALUES(`phone`),
  `status` = VALUES(`status`),
  `is_admin` = VALUES(`is_admin`);

-- ----------------------------------------------------------------------------
-- 5. Seed Customer Address Book
-- ----------------------------------------------------------------------------
DELETE FROM `user_addresses`;
INSERT INTO `user_addresses` (`id`, `user_id`, `recipient_name`, `phone`, `street_address`, `apartment_unit`, `city`, `state_province`, `postal_code`, `country`, `is_default`) VALUES
(1, 7, 'Martha Jenkins', '+1 (555) 019-2837', '742 Evergreen Terrace', 'Apt 4B', 'Springfield', 'IL', '62704', 'United States', 1),
(2, 7, 'Martha Jenkins (Summer Cottage)', '+1 (555) 019-2837', '18 Lakeview Road', NULL, 'Lake Geneva', 'WI', '53147', 'United States', 0);

-- ----------------------------------------------------------------------------
-- 6. Seed Product Categories
-- ----------------------------------------------------------------------------
INSERT INTO `categories` (`id`, `slug`, `name`, `description`, `icon_class`, `image`, `is_active`) VALUES
(1, 'common-things', 'Common Things', 'Everyday over-the-counter essentials, pain relievers, and first-aid supplies', 'bx-plus-medical', 'common.html', 1),
(2, 'kids-items',    'Kids Items',    'Pediatric syrups, infant drops, chewables, and nutritional formulations', 'bx-child',        'kids.html',   1),
(3, 'womens-items',  'Women Items',  'Maternal care, prenatal vitamins, skincare, and female wellness',          'bx-female',       'womens.html', 1),
(4, 'mens-items',    'Men Items',    'Cardiovascular support, vitality supplements, and daily men’s health',    'bx-male',         'mens.html',   1),
(5, 'animal-care',   'Animal Care',  'Veterinary antibiotics, pet wound care, joint supplements, and sprays',    'bx-bone',         'animal.html', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`);

-- ----------------------------------------------------------------------------
-- 7. Seed Products Master Catalog (Clinical Formulation Details)
-- ----------------------------------------------------------------------------
DELETE FROM `products`;
INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `generic_name`, `slug`, `description`, `dosage_form`, `strength`, `pack_size`, `price`, `cost_price`, `requires_prescription`, `low_stock_threshold`, `image`, `is_active`) VALUES
(1,  1, 'MED-OTC-001', 'Panadol Extra', 'Paracetamol / Caffeine', 'panadol-extra', 'Fast-acting analgesic and antipyretic for severe headaches, dental pain, fever, and muscular aches. Enhanced absorption formula.', 'tablet', '500mg / 65mg', '24 Tablets / Pack', 2.00, 0.90, 0, 15, 'panadol.jpg', 1),
(2,  1, 'MED-RX-002',  'Amoxicillin Trihydrate', 'Amoxicillin', 'amoxicillin-500mg', 'Broad-spectrum beta-lactam antibiotic for bacterial respiratory, ENT, and urinary tract infections. Prescription verification mandatory.', 'capsule', '500mg', '30 Capsules / Box', 12.50, 5.20, 1, 10, 'amox.jpeg', 1),
(3,  1, 'MED-RX-003',  'Atrolip (Atorvastatin)', 'Atorvastatin Calcium', 'atrolip-20mg', 'HMG-CoA reductase inhibitor for managing primary hypercholesterolemia and cardiovascular risk reduction.', 'tablet', '20mg', '30 Tablets / Pack', 18.00, 7.80, 1, 10, 'atrolip.jpeg', 1),
(4,  2, 'MED-KID-004', 'Zin Tablet (Zinc Supplement)', 'Zinc Gluconate', 'zin-tablet', 'Chewable pediatric dietary zinc supplement to boost immune function and support growth in children.', 'tablet', '20mg', '50 Chewables / Bottle', 0.50, 0.20, 0, 20, 'zin.jpeg', 1),
(5,  3, 'MED-WMN-005', 'Vitamin C Chewable', 'Ascorbic Acid / Sodium Ascorbate', 'vitamin-c-500mg', 'High-potency antioxidant formulation supporting immune resistance, collagen synthesis, and iron absorption.', 'tablet', '500mg', '60 Chewables / Bottle', 4.50, 1.80, 0, 25, 'c.jpeg', 1),
(6,  1, 'MED-OTC-006', 'Non-Fat Milk Powder', 'Fortified Skimmed Milk Formulation', 'non-fat-milk-powder', 'Nutritionally balanced high-calcium non-fat milk powder suitable for diabetic and cardiac diet regimens.', 'powder', '100% Pure Skimmed', '400g Foil Pouch', 5.00, 2.75, 0, 15, 'non.jpeg', 1),
(7,  1, 'MED-AID-007', 'Zart Sterile Wound Plaster Box', 'Adhesive Surgical Strip Plasters', 'zart-plaster-box', 'Waterproof hypoallergenic wound dressing strips with antiseptic pad for cuts, scrapes, and minor abrasions.', 'powder', 'Assorted Sizes', '100 Strips / Box', 1.50, 0.60, 0, 30, 'zart.jpg', 1),
(8,  1, 'MED-HYG-008', 'Tooth Brush Multi-Action', 'Micro-Bristle Dental Care', 'tooth-brush-multi-action', 'Ergonomic soft micro-bristle toothbrush with tongue cleaner for plaque removal and sensitive gum protection.', 'powder', 'Soft Bristles', '1 Unit Sealed', 1.00, 0.35, 0, 25, 'tooth.png', 1),
(9,  1, 'MED-HYG-009', 'Herbal Antiseptic Soap', 'Neem & Aloe Vera Extract', 'herbal-antiseptic-soap', 'Medicated botanical antiseptic cleansing bar for skin dermatological protection and daily hygiene.', 'cream', '100g Bar', '1 Bar', 1.00, 0.40, 0, 20, 'herbal.png', 1),
(10, 5, 'MED-VET-010', 'Veterinary Broad-Spectrum Antiseptic', 'Povidone-Iodine Veterinary Grade', 'vet-antiseptic-solution', 'Topical disinfectant solution for treating surgical wounds, skin infections, and ringworm in domestic animals.', 'drops', '10% Topical', '250ml Bottle', 8.50, 3.80, 0, 10, 'been.png', 1),
(11, 4, 'MED-MAN-011', 'CardioVital CoQ10 Support', 'Coenzyme Q10 / Vitamin E', 'cardiovital-coq10', 'Advanced antioxidant cardiac formulation for cellular energy production, vascular health, and arterial support.', 'capsule', '100mg', '60 Softgels / Bottle', 22.00, 9.50, 0, 12, 'corona.png', 1),
(12, 1, 'MED-AID-012', 'Comprehensive First Aid Kit', 'Emergency Trauma Supplies', 'first-aid-kit', 'Hospital-grade portable medical emergency kit containing bandages, antiseptic wipes, shears, gauze, and tape.', 'powder', 'Complete 85-piece', '1 Hardcase Kit', 18.50, 8.20, 0, 8, 'aids.png', 1),
(13, 2, 'MED-KID-013', 'Pediatric Paracetamol Syrup', 'Paracetamol Pediatric Oral Liquid', 'pediatric-paracetamol-syrup', 'Gentle strawberry-flavored suspension for rapid relief of fever and pain in infants and young children.', 'syrup', '120mg / 5ml', '100ml Bottle', 3.20, 1.10, 0, 20, 'johny.png', 1),
(14, 5, 'MED-VET-014', 'Canine & Feline Dewormer', 'Pyrantel Pamoate / Praziquantel', 'pet-dewormer-tablets', 'Broad-spectrum veterinary deworming treatment against tapeworms, roundworms, hookworms, and whipworms.', 'tablet', 'Veterinary Formula', '6 Chewables / Pack', 14.00, 6.00, 0, 15, 'hiv.png', 1);

-- ----------------------------------------------------------------------------
-- 8. Synchronize Legacy 'members' Table (Used by Existing Admin Panel)
-- ----------------------------------------------------------------------------
DELETE FROM `members`;
INSERT INTO `members` (`id`, `itemname`, `type`, `quantity`, `itemdetail`, `image`) VALUES
(1,  'Panadol Extra',                      'Common Things', 85, 'Paracetamol 500mg + Caffeine 65mg pain reliever & fever reducer', 'panadol.jpg'),
(2,  'Amoxicillin Trihydrate 500mg',       'Common Things', 45, 'Antibiotic for bacterial infections. Prescription Required.',       'amox.jpeg'),
(3,  'Atrolip (Atorvastatin 20mg)',        'Common Things', 35, 'Cholesterol management medication. Prescription Required.',        'atrolip.jpeg'),
(4,  'Zin Tablet (Zinc Supplement)',       'Kids Items',    60, 'Nutritional zinc chewables for pediatric immune health',             'zin.jpeg'),
(5,  'Vitamin C Chewable 500mg',           'Women Items',   95, 'High-potency antioxidant ascorbic acid daily wellness',              'c.jpeg'),
(6,  'Non-Fat Milk Powder',                'Common Things', 40, 'Fortified skimmed milk powder for dietary management',               'non.jpeg'),
(7,  'Zart Wound Plaster Box',             'Common Things', 70, 'Waterproof hypoallergenic wound dressing strips (100 pack)',        'zart.jpg'),
(8,  'Tooth Brush Multi-Action',           'Common Things', 55, 'Ergonomic soft micro-bristle toothbrush for sensitive care',         'tooth.png'),
(9,  'Herbal Antiseptic Soap',             'Common Things', 80, 'Neem & Aloe vera medicated cleansing bar for daily hygiene',         'herbal.png'),
(10, 'Veterinary Antiseptic Solution',     'Animal Care',   30, 'Topical disinfectant solution for domestic animal wound treatment',  'been.png'),
(11, 'CardioVital CoQ10 Support',          'Men Items',     25, 'Cellular cardiac energy formulation for vascular wellness',          'corona.png'),
(12, 'Comprehensive First Aid Kit',        'Common Things', 18, '85-piece emergency response kit with trauma shears and dressings',   'aids.png'),
(13, 'Pediatric Paracetamol Syrup',        'Kids Items',    50, 'Gentle strawberry oral suspension for children fever relief',        'johny.png'),
(14, 'Canine & Feline Dewormer',           'Animal Care',   28, 'Broad-spectrum veterinary deworming chewable tablets',               'hiv.png');

-- ----------------------------------------------------------------------------
-- 9. Seed Inventory Batches & FEFO Expiry Tracking
-- ----------------------------------------------------------------------------
DELETE FROM `inventory_batches`;
INSERT INTO `inventory_batches` (`product_id`, `batch_number`, `quantity`, `allocated_quantity`, `manufacturing_date`, `expiry_date`, `supplier_name`) VALUES
-- Panadol Extra: 2 batches (one expiring sooner, one later)
(1, 'LOT-GSK-2025-01',  35, 0, '2025-01-10', '2027-01-10', 'GlaxoSmithKline Healthcare'),
(1, 'LOT-GSK-2025-02',  50, 0, '2025-06-15', '2027-06-15', 'GlaxoSmithKline Healthcare'),

-- Amoxicillin: 2 batches (one near expiry < 90 days for FEFO demonstration)
(2, 'LOT-PFZ-2024-X8',  15, 0, '2024-03-01', DATE_ADD(CURDATE(), INTERVAL 45 DAY), 'Pfizer Pharmaceuticals'),
(2, 'LOT-PFZ-2025-A1',  30, 0, '2025-02-10', '2027-08-20', 'Pfizer Pharmaceuticals'),

-- Atrolip: 1 batch
(3, 'LOT-NOV-2025-C3',  35, 0, '2025-01-20', '2027-12-15', 'Novartis Pharma'),

-- Zin Tablet: 1 batch
(4, 'LOT-CIP-2025-Z2',  60, 0, '2025-04-05', '2028-04-05', 'Cipla Health Care'),

-- Vitamin C: 2 batches (one near expiry < 60 days)
(5, 'LOT-BYR-2024-V9',  25, 0, '2024-05-12', DATE_ADD(CURDATE(), INTERVAL 55 DAY), 'Bayer Consumer Health'),
(5, 'LOT-BYR-2025-V1',  70, 0, '2025-03-18', '2028-03-18', 'Bayer Consumer Health'),

-- Other items
(6,  'LOT-LAC-2025-M1', 40, 0, '2025-02-01', '2026-12-31', 'Lactalis Nutrition'),
(7,  'LOT-ZRT-2025-P4', 70, 0, '2025-01-15', '2029-01-15', 'Zart MedTech Corp'),
(8,  'LOT-ORA-2025-T1', 55, 0, '2025-03-01', '2030-03-01', 'OralCare International'),
(9,  'LOT-HRB-2025-S2', 80, 0, '2025-02-14', '2027-02-14', 'Botanical Health Labs'),
(10, 'LOT-VET-2025-B5', 30, 0, '2025-01-05', '2027-07-05', 'Zoetis Animal Health'),
(11, 'LOT-CRD-2025-Q7', 25, 0, '2025-05-10', '2028-05-10', 'CardioVascular Life Sciences'),
(12, 'LOT-EMG-2025-A9', 18, 0, '2025-01-01', '2029-01-01', 'FirstAid Rescue Systems'),
(13, 'LOT-KID-2025-J3', 50, 0, '2025-04-12', '2027-10-12', 'Johnson & Johnson Pediatrics'),
(14, 'LOT-PET-2025-H8', 28, 0, '2025-02-20', '2028-02-20', 'Elanco Animal Health');

-- ----------------------------------------------------------------------------
-- 10. Seed Prescriptions (Clinical Triage Queue Demonstration)
-- ----------------------------------------------------------------------------
DELETE FROM `prescriptions`;
INSERT INTO `prescriptions` (`id`, `user_id`, `doctor_name`, `doctor_license`, `patient_name`, `file_path`, `file_mime`, `file_size_bytes`, `status`, `reviewed_by`, `pharmacist_notes`, `reviewed_at`) VALUES
(1, 7, 'Dr. Robert Harrison, MD', 'MD-NY-849201', 'Martha Jenkins', 'storage/prescriptions/rx_martha_amox.pdf', 'application/pdf', 245120, 'approved', 4, 'Prescription verified with Dr. Harrison clinic. Dosage confirmed: Amoxicillin 500mg tid x 10 days.', NOW()),
(2, 7, 'Dr. Robert Harrison, MD', 'MD-NY-849201', 'Martha Jenkins', 'storage/prescriptions/rx_martha_atrolip.pdf', 'application/pdf', 198450, 'pending', NULL, NULL, NULL);

-- ----------------------------------------------------------------------------
-- 11. Seed Orders & Order Items
-- ----------------------------------------------------------------------------
DELETE FROM `order_items`;
DELETE FROM `order_status_history`;
DELETE FROM `orders`;

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `shipping_address_id`, `prescription_id`, `subtotal`, `tax_amount`, `shipping_fee`, `discount_amount`, `total_amount`, `order_status`, `assigned_courier_id`, `delivery_notes`) VALUES
(1, 'ORD-2026-0001', 7, 1, 1, 16.50, 1.32, 4.99, 0.00, 22.81, 'delivered', 6, 'Please leave with reception if front gate is closed.'),
(2, 'ORD-2026-0002', 7, 1, 2, 22.50, 1.80, 0.00, 0.00, 24.30, 'rx_review', NULL, 'Pending pharmacist review of Atrolip prescription.');

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `batch_id`, `unit_price`, `quantity`, `line_total`) VALUES
(1, 1, 1, 1, 2.00,  2, 4.00),
(2, 1, 2, 3, 12.50, 1, 12.50),
(3, 2, 3, 5, 18.00, 1, 18.00),
(4, 2, 5, 8, 4.50,  1, 4.50);

INSERT INTO `order_status_history` (`order_id`, `from_status`, `to_status`, `comments`, `updated_by`) VALUES
(1, 'pending_payment', 'processing', 'Payment verified and prescription approved', 1),
(1, 'processing', 'dispatched', 'Package assigned to courier Leo Rodriguez', 3),
(1, 'dispatched', 'delivered', 'Successfully handed to recipient Martha Jenkins', 6),
(2, 'pending_payment', 'rx_review', 'Awaiting clinical prescription verification', 1);

-- ----------------------------------------------------------------------------
-- 12. Seed Payments (PCI-DSS Compliant Non-Sensitive Transaction Logs)
-- ----------------------------------------------------------------------------
DELETE FROM `payments`;
INSERT INTO `payments` (`id`, `order_id`, `card_type`, `card_last4`, `transaction_ref`, `expiry_date`, `payment_status`, `amount`, `created_at`) VALUES
(1, 1, 'Visa',       '4242', 'TXN-PL-2026-08912A', '08/2028', 'completed', 22.81, NOW()),
(2, 2, 'MasterCard', '5555', 'TXN-PL-2026-08913B', '11/2027', 'completed', 24.30, NOW());

-- ----------------------------------------------------------------------------
-- 13. Seed Customer Reviews (Verified Purchases)
-- ----------------------------------------------------------------------------
DELETE FROM `reviews`;
INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `headline`, `comment`, `is_verified_purchase`, `status`) VALUES
(1, 1, 7, 5, 'Fast acting relief for tension headaches', 'Panadol Extra worked within 20 minutes for my tension headache. Very gentle on my stomach and fast delivery.', 1, 'approved'),
(2, 5, 7, 5, 'Great taste and boosts energy', 'These chewable vitamin C tablets taste great, not overly sour. Excellent daily immune support.', 1, 'approved');

-- ----------------------------------------------------------------------------
-- 14. Seed System Settings
-- ----------------------------------------------------------------------------
INSERT INTO `system_settings` (`key_name`, `value_data`, `description`) VALUES
('store_name',                 'PharmaLink Online Pharmacy', 'Public brand name of the dispensary'),
('currency',                   'USD',                        'Standard ISO currency code'),
('currency_symbol',            '$',                          'Display currency symbol'),
('tax_rate',                   '0.08',                       'Sales tax rate applied to subtotal (8%)'),
('shipping_flat_fee',          '4.99',                       'Standard ground delivery flat fee in USD'),
('free_shipping_threshold',    '50.00',                      'Minimum subtotal order value to qualify for free delivery'),
('low_stock_default_threshold','10',                         'Default inventory threshold for low-stock warnings'),
('rx_mandatory_check',         '1',                          'Enforce pharmacist review for prescription-flagged items'),
('support_email',              'support@pharmalink.test',    'Clinical inquiry support email address')
ON DUPLICATE KEY UPDATE `value_data` = VALUES(`value_data`);

SET FOREIGN_KEY_CHECKS = 1;
