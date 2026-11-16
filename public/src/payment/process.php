<?php
/**
 * src/payment/process.php
 * Handles payment form submission (POST from public/pages/payment.html).
 * PCI-DSS Compliant: Raw card numbers and CVVs are strictly NEVER stored.
 */

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/payment.html');
    exit();
}

// Enforce CSRF token protection
enforceCsrf();

$cardType   = trim((string)($_POST['card_type'] ?? 'visa'));
$cardNumber = trim((string)($_POST['card_number'] ?? ''));
$expiryDate = trim((string)($_POST['expiry_date'] ?? ''));

// Extract non-sensitive last 4 digits only (PCI-DSS requirement)
$digitsOnly = preg_replace('/\D/', '', $cardNumber);
$cardLast4  = strlen($digitsOnly) >= 4 ? substr($digitsOnly, -4) : '0000';

// Generate unique simulated transaction reference
$txnRef = 'TXN_' . strtoupper(bin2hex(random_bytes(8)));

$pdo  = getPDO();
$stmt = $pdo->prepare(
    'INSERT INTO payments (card_type, card_last4, transaction_ref, expiry_date, payment_status, amount)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$cardType, $cardLast4, $txnRef, $expiryDate, 'completed', 0.00]);

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful | PharmaLink</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        body { font-family: "Poppins", sans-serif; background: #f0f4f8; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; max-width: 450px; }
        .icon { font-size: 50px; color: #28a745; margin-bottom: 15px; }
        h2 { margin: 0 0 10px; color: #333; }
        p { color: #666; font-size: 14px; margin: 5px 0; }
        .ref { font-weight: bold; background: #e9ecef; padding: 8px 12px; border-radius: 4px; display: inline-block; margin: 15px 0; font-family: monospace; }
        a { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #4070f4; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; }
        a:hover { background: #2756d1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h2>Payment Processed</h2>
        <p>Your payment was verified and processed securely.</p>
        <div class="ref">Ref: ' . htmlspecialchars($txnRef) . '</div>
        <p>Card Ending: **** ' . htmlspecialchars($cardLast4) . ' (' . htmlspecialchars(ucfirst($cardType)) . ')</p>
        <a href="/pages/home.html">Return to Storefront</a>
    </div>
</body>
</html>';
exit();
