<?php
/**
 * src/config/csrf_token.php
 * Endpoint providing the session CSRF token to client-side views.
 */

declare(strict_types=1);

require_once __DIR__ . '/csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

echo json_encode([
    'csrf_token' => getCsrfToken()
], JSON_THROW_ON_ERROR);
