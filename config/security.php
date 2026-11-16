<?php
/**
 * config/security.php
 * Hardened Security Configurations & Cryptographic Policies.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

return [
    'session' => [
        'lifetime'  => 86400, // 24 hours
        'path'      => '/',
        'domain'    => '',
        'secure'    => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443),
        'httponly'  => true,
        'samesite'  => 'Lax',
    ],
    'csrf' => [
        'token_length' => 32, // bytes (64 hex characters)
        'header_name'  => 'X-CSRF-TOKEN',
    ],
    'password' => [
        'algorithm' => PASSWORD_BCRYPT,
        'cost'      => 12,
    ],
    'rate_limiting' => [
        'max_login_attempts' => 5,
        'lockout_minutes'    => 15,
    ]
];
