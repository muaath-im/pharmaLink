<?php
/**
 * public/index.php
 * Application Entry Point & Hybrid Routing Gateway.
 * Preserves 100% legacy redirect behavior for '/' while enabling modern MVC routes.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// 1. Root and index redirects: 100% backward compatible legacy behavior
if ($path === '/' || $path === '/index.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['email'])) {
        header('Location: /pages/home.html');
    } else {
        header('Location: /pages/auth.html');
    }
    exit();
}

// 2. Dispatch modern MVC routes (e.g. /home, /catalog, /api/v1/*)
require_once dirname(__DIR__) . '/app/bootstrap.php';

$app = \App\Application::getInstance();
$app->run();

