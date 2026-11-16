<?php
/**
 * src/delivery/register.php
 * Handles delivery registration form (POST from public/pages/delivery.html).
 * Enforces anti-CSRF token verification.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/delivery.html');
    exit();
}

// Enforce CSRF token protection
enforceCsrf();

$name      = trim((string)($_POST['name'] ?? ''));
$address   = trim((string)($_POST['address'] ?? ''));
$mail      = trim((string)($_POST['mail'] ?? ''));
$telephone = trim((string)($_POST['telephone'] ?? ''));
$city      = trim((string)($_POST['city'] ?? ''));
$postal    = trim((string)($_POST['postal'] ?? ''));
$order_yes = isset($_POST['YoN']) ? 1 : 0;

if ($name === '' || $address === '' || $mail === '') {
    echo 'Please fill in all required delivery fields.';
    exit();
}

$pdo  = getPDO();
$stmt = $pdo->prepare(
    'INSERT INTO delivery_registration (name, address, mail, telephone, city, postal, order_yes)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$name, $address, $mail, $telephone, $city, $postal, $order_yes]);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Confirmed | PharmaLink</title>
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
        <h2>Delivery Registered</h2>
        <p>Recipient: <strong>' . htmlspecialchars($name) . '</strong></p>
        <p>Destination: <strong>' . htmlspecialchars($address) . ', ' . htmlspecialchars($city) . '</strong></p>
        <p>A dispatch confirmation was assigned to <strong>' . htmlspecialchars($mail) . '</strong>.</p>
        <a href="/pages/home.html">Return to Storefront</a>
    </div>
</body>
</html>';
exit();
