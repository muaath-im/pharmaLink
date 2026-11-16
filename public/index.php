<?php
/**
 * public/index.php
 * Application entry point.
 * Redirects logged-in users to home, others to auth.
 */

declare(strict_types=1);

session_start();

if (isset($_SESSION['email'])) {
    header('Location: /pages/home.html');
} else {
    header('Location: /pages/auth.html');
}
exit();
