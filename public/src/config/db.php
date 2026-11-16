<?php
/**
 * src/config/db.php
 * Single canonical database connection for PharmaLink.
 * Reads credentials from .env (loaded once here).
 * Returns a PDO instance via getPDO().
 */

declare(strict_types=1);

/**
 * Load key=value pairs from a .env file into $_ENV / getenv().
 * Only runs once per request; ignores comment lines and blanks.
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load .env from project root
$rootEnv = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
$parentEnv = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . '.env';
if (file_exists($rootEnv)) {
    loadEnv($rootEnv);
} elseif (file_exists($parentEnv)) {
    loadEnv($parentEnv);
}

/**
 * Return a PDO connection using .env credentials.
 * Throws PDOException on failure (will surface as 500 in dev).
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost';
    $name = getenv('DB_NAME') ?: 'pharmalink';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $pdo = new PDO(
        "mysql:host=$host;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    return $pdo;
}
