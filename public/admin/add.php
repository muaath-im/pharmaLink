<?php
/**
 * admin/add.php
 * Handles creating a new medicine/inventory item in the admin panel.
 * Enforces admin authorization and anti-CSRF token verification.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/src/config/auth_guard.php';
require_once dirname(__DIR__) . '/src/config/csrf.php';
require_once __DIR__ . '/db.php';

// Enforce admin privileges
requireAdmin();

if (isset($_POST['add'])) {
    // Enforce CSRF token verification
    enforceCsrf();

    $itemname   = trim((string)($_POST['itemname'] ?? ''));
    $type       = trim((string)($_POST['type'] ?? ''));
    $quantity   = max(0, (int)($_POST['quantity'] ?? 0));
    $itemdetail = trim((string)($_POST['itemdetail'] ?? ''));
    $image      = trim((string)($_POST['image'] ?? ''));

    if ($itemname === '' || $type === '') {
        $_SESSION['error'] = 'Item name and category are required.';
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare('INSERT INTO members (itemname, type, quantity, itemdetail, image) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('ssiss', $itemname, $type, $quantity, $itemdetail, $image);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Item added successfully.';
    } else {
        $_SESSION['error'] = 'Failed to add item: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Please fill out the add form first.';
}

header('Location: index.php');
exit();
