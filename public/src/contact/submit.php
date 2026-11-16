<?php
/**
 * src/contact/submit.php
 * Handles contact form submission (POST from public/pages/contact.html).
 * Enforces anti-CSRF token verification.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/contact.html');
    exit();
}

// Enforce CSRF token protection
enforceCsrf();

$name  = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$topic = trim((string)($_POST['topic'] ?? ''));
$msg   = trim((string)($_POST['msg'] ?? ''));

if ($name === '' || $email === '' || $topic === '' || $msg === '') {
    echo 'Please fill in all fields.';
    exit();
}

$pdo  = getPDO();
$stmt = $pdo->prepare(
    'INSERT INTO contact (name, email, topic, message) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$name, $email, $topic, $msg]);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inquiry Received | PharmaLink</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        body { font-family: "Poppins", sans-serif; background: #f0f4f8; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; max-width: 450px; }
        .icon { font-size: 50px; color: #28a745; margin-bottom: 15px; }
        h2 { margin: 0 0 10px; color: #333; }
        p { color: #666; font-size: 14px; margin: 5px 0; }
        a { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #4070f4; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; }
        a:hover { background: #2756d1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h2>Thank You, ' . htmlspecialchars($name) . '!</h2>
        <p>Your inquiry regarding <strong>' . htmlspecialchars($topic) . '</strong> has been received by our clinical support team.</p>
        <p>We will respond to <strong>' . htmlspecialchars($email) . '</strong> shortly.</p>
        <a href="/pages/home.html">Back to Home</a>
    </div>
</body>
</html>';
exit();
