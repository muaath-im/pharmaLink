<?php
/**
 * config/database.php
 * Database Connection & Connection Pool Settings.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => getenv('DB_HOST') ?: '127.0.0.1',
            'port'      => (int)(getenv('DB_PORT') ?: 3306),
            'database'  => getenv('DB_NAME') ?: 'pharmalink',
            'username'  => getenv('DB_USER') ?: 'root',
            'password'  => getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'root',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        ]
    ]
];
