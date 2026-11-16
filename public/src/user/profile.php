<?php
/**
 * src/user/profile.php
 * Protected user profile page.
 * Redirects to auth if not logged in.
 * Shows current user info + their own delivery registrations.
 */

declare(strict_types=1);

session_start();

if (!isset($_SESSION['email'])) {
    header('Location: /pages/auth.html');
    exit();
}

require_once dirname(__DIR__) . '/config/db.php';

$pdo   = getPDO();
$email = $_SESSION['email'];

// Fetch logged-in user
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo 'Error: User not found';
    exit();
}

// Fetch only THIS user's delivery registrations (filtered by email)
$dStmt = $pdo->prepare(
    'SELECT name, address, mail, telephone, city, postal, order_yes, created_at
     FROM delivery_registration WHERE mail = ?'
);
$dStmt->execute([$email]);
$deliveries = $dStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | PharmaLink</title>
    <link rel="icon" type="image/x-icon" href="/assets/images/logo.png">
    <link rel="stylesheet" href="/assets/css/profile.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
</head>
<body>
    <header class="sticky">
        <a href="/pages/home.html" class="logo"><img src="/assets/images/logo.png" alt="PharmaLink logo"></a>
        <ul class="menu">
            <li><a href="/pages/home.html">Home</a></li>
            <li><a href="/pages/delivery.html">Delivery</a></li>
            <li><a href="/pages/categories.html">Categories</a></li>
            <li><a href="/pages/contact.html">Contact Us</a></li>
            <li><a href="/pages/about.html">About Us</a></li>
        </ul>
        <div class="icon">
            <a href="/pages/search.html"><i class='bx bx-search'></i></a>
            <a href="/src/user/profile.php"><i class='bx bx-user'></i></a>
            <a href="/src/auth/logout.php"><i class='bx bx-log-out'></i></a>
            <div class="bx bx-menu" id="icon"></div>
        </div>
    </header>

    <br><br><br><br><br><br><br>
    <h2>User Profile</h2>
    <table>
        <tr><th colspan="2">User Information</th></tr>
        <tr><td>Name</td>    <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td></tr>
        <tr><td>Email</td>   <td><?php echo htmlspecialchars($user['email']    ?? 'N/A'); ?></td></tr>
        <tr><td>Address</td> <td><?php echo htmlspecialchars($user['address']  ?? 'N/A'); ?></td></tr>
        <tr><td>Age</td>     <td><?php echo htmlspecialchars((string)($user['age'] ?? 'N/A')); ?></td></tr>
    </table>

    <div class="container">
        <h1 style="color:blue">MY DELIVERY ORDERS</h1>
        <table id="productTable" style="background-color:aliceblue">
            <tr>
                <th>Name</th><th>Address</th><th>Email</th><th>Telephone</th>
                <th>City</th><th>Postal</th><th>Order</th><th>Date</th>
            </tr>
            <?php if (empty($deliveries)): ?>
                <tr><td colspan="8">No delivery records found.</td></tr>
            <?php else: ?>
                <?php foreach ($deliveries as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']);       ?></td>
                    <td><?php echo htmlspecialchars($row['address']);    ?></td>
                    <td><?php echo htmlspecialchars($row['mail']);       ?></td>
                    <td><?php echo htmlspecialchars($row['telephone']);  ?></td>
                    <td><?php echo htmlspecialchars($row['city']);       ?></td>
                    <td><?php echo htmlspecialchars($row['postal']);     ?></td>
                    <td><?php echo $row['order_yes'] ? 'Yes' : 'No';   ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
