<?php
/**
 * src/config/csrf.php
 * Anti-CSRF Synchronizer Token Protection Utility.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate or retrieve the existing CSRF token for the session.
 */
function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

/**
 * Output an HTML hidden input tag containing the CSRF token.
 */
function csrfField(): string
{
    $token = htmlspecialchars(getCsrfToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Validate the provided CSRF token against the session token.
 */
function validateCsrfToken(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Enforce CSRF token validation on POST requests.
 * Exits with HTTP 403 Forbidden if invalid.
 */
function enforceCsrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? null;
        if (!validateCsrfToken($token)) {
            http_response_code(403);
            die('Error 403: Invalid or missing CSRF token. Request aborted for your security.');
        }
    }
}
