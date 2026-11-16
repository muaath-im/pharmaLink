<?php
/**
 * admin/db.php
 * Database connection for the PharmaLink admin panel.
 * Uses the shared .env loader from src/config/db.php.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config/db.php';

// MySQLi OOP connection for admin panel (DataTables integration requires MySQLi)
$_env_host = getenv('DB_HOST') ?: 'localhost';
$_env_name = getenv('DB_NAME') ?: 'pharmalink';
$_env_user = getenv('DB_USER') ?: 'root';
$_env_pass = getenv('DB_PASS') ?: '';

$conn = new mysqli($_env_host, $_env_user, $_env_pass, $_env_name);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
