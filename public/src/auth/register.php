<?php
/**
 * src/auth/register.php
 * Handles new user signup (POST from public/pages/auth.html).
 * Enforces anti-CSRF protection and cryptographic password hashing (Bcrypt).
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['Signup'])) {
    header('Location: /pages/auth.html');
    exit();
}

// Enforce CSRF protection
enforceCsrf();

$pdo      = getPDO();
$username = trim((string)($_POST['Uname'] ?? ''));
$address  = trim((string)($_POST['address'] ?? ''));
$age      = (int)($_POST['Age'] ?? 0);
$email    = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');

if ($email === '' || $password === '' || $username === '') {
    echo 'Please fill in all required registration fields.';
    exit();
}

// Check for existing email (case-insensitive)
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo 'Email Address Already Exists!';
    exit();
}

// Hash password with Bcrypt (cost 12)
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Insert new user with hashed password
$ins = $pdo->prepare(
    'INSERT INTO users (username, address, age, email, password, is_admin) VALUES (?, ?, ?, ?, ?, 0)'
);
$ins->execute([$username, $address, $age, $email, $passwordHash]);

header('Location: /pages/auth.html');
exit();
