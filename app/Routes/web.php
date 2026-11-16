<?php
/**
 * app/Routes/web.php
 * Web Browser Route Definitions.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\CatalogController;
use App\Core\Middleware\SecurityHeadersMiddleware;

/** @var Router $router */

// Global Security Middleware for web
$webMiddleware = [
    SecurityHeadersMiddleware::class
];

$router->get('/home', [HomeController::class, 'index'], $webMiddleware);
$router->get('/catalog', [CatalogController::class, 'index'], $webMiddleware);
$router->get('/catalog/{slug}', [CatalogController::class, 'show'], $webMiddleware);
