<?php
/**
 * admin/delete.php
 * Handles deleting an item from the admin panel.
 * Enforces admin authorization, POST method requirement, and anti-CSRF token verification.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/src/config/auth_guard.php';
require_once dirname(__DIR__) . '/src/config/csrf.php';
require_once __DIR__ . '/db.php';

// Enforce admin privileges
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Enforce CSRF token verification
    enforceCsrf();

    $id = (int)$_POST['id'];

    if ($id <= 0) {
        $_SESSION['error'] = 'Invalid item ID for deletion.';
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare('DELETE FROM members WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Item deleted successfully.';
    } else {
        $_SESSION['error'] = 'Failed to delete item: ' . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Invalid delete request.';
}

header('Location: index.php');
exit();
