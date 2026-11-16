<?php
/**
 * src/config/auth_guard.php
 * Role-Based Access Control and Authentication Guards.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is currently authenticated.
 */
function isAuthenticated(): bool
{
    return !empty($_SESSION['email']);
}

/**
 * Check if the currently authenticated user is an administrator.
 */
function isAdmin(): bool
{
    return isAuthenticated() && !empty($_SESSION['is_admin']) && ((int)$_SESSION['is_admin'] === 1);
}

/**
 * Restrict access to authenticated users only.
 */
function requireAuth(): void
{
    if (!isAuthenticated()) {
        header('Location: /pages/auth.html');
        exit();
    }
}

/**
 * Restrict access to authenticated administrators only.
 */
function requireAdmin(): void
{
    if (!isAuthenticated()) {
        header('Location: /pages/auth.html');
        exit();
    }
    if (!isAdmin()) {
        http_response_code(403);
        echo '<!DOCTYPE html>
        <html>
        <head><title>403 Forbidden | PharmaLink</title></head>
        <body style="font-family: sans-serif; text-align: center; padding: 50px;">
            <h1 style="color: #dc3545;">403 Forbidden</h1>
            <p>Access Denied: You do not have administrative privileges to view this page.</p>
            <p><a href="/pages/home.html">Return to Storefront</a></p>
        </body>
        </html>';
        exit();
    }
}
