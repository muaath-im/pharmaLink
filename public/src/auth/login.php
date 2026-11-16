<?php
/**
 * src/auth/login.php
 * Handles user login (POST from public/pages/auth.html).
 * Enforces anti-CSRF, Bcrypt password verification, and session fixation protection.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/auth.html');
    exit();
}

// Enforce CSRF protection
enforceCsrf();

$pdo      = getPDO();
$email    = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    echo 'Please enter both email and password.';
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    $authenticated = false;

    // 1. Verify modern cryptographically hashed password
    if (password_verify($password, (string)$user['password'])) {
        $authenticated = true;

        // Check if hash algorithm requires upgrade
        if (password_needs_rehash((string)$user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $rehashStmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $rehashStmt->execute([$newHash, $user['id']]);
        }
    }
    // 2. Backward compatibility for legacy plain-text passwords (auto-upgrade on login)
    elseif ($password === (string)$user['password']) {
        $authenticated = true;
        $upgradedHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $upgradeStmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $upgradeStmt->execute([$upgradedHash, $user['id']]);
    }

    if ($authenticated) {
        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']  = (int)$user['id'];
        $_SESSION['username'] = (string)$user['username'];
        $_SESSION['email']    = (string)$user['email'];
        $_SESSION['is_admin'] = (int)($user['is_admin'] ?? 0);

        // Redirect admin to admin dashboard, customer to home
        if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1) {
            header('Location: /admin/index.php');
        } else {
            header('Location: /pages/home.html');
        }
        exit();
    }
}

echo 'Not Found, Incorrect Email or Password';
exit();
