<?php
/**
 * admin/edit.php
 * Handles editing an existing item in the admin panel.
 * Enforces admin authorization and anti-CSRF token verification.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/src/config/auth_guard.php';
require_once dirname(__DIR__) . '/src/config/csrf.php';
require_once __DIR__ . '/db.php';

// Enforce admin privileges
requireAdmin();

if (isset($_POST['edit'])) {
    // Enforce CSRF token verification
    enforceCsrf();

    $id         = (int)($_POST['id'] ?? 0);
    $itemname   = trim((string)($_POST['itemname'] ?? ''));
    $type       = trim((string)($_POST['type'] ?? ''));
    $quantity   = max(0, (int)($_POST['quantity'] ?? 0));
    $itemdetail = trim((string)($_POST['itemdetail'] ?? ''));
    $image      = trim((string)($_POST['image'] ?? ''));

    if ($id <= 0 || $itemname === '') {
        $_SESSION['error'] = 'Invalid item data provided.';
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare('UPDATE members SET itemname = ?, type = ?, quantity = ?, itemdetail = ?, image = ? WHERE id = ?');
    $stmt->bind_param('ssissi', $itemname, $type, $quantity, $itemdetail, $image, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Item updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update item: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Select an item to edit first.';
}

header('Location: index.php');
exit();
