<?php
/**
 * app/Routes/api.php
 * RESTful JSON API Route Definitions.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\ApiController;
use App\Core\Middleware\SecurityHeadersMiddleware;

/** @var Router $router */

$apiMiddleware = [
    SecurityHeadersMiddleware::class
];

$router->group('/api/v1', $apiMiddleware, function (Router $r) {
    $r->get('/health', [ApiController::class, 'health']);
    $r->get('/products', [ApiController::class, 'products']);
    $r->get('/products/{idOrSlug}', [ApiController::class, 'productDetail']);
    $r->get('/batches/near-expiry', [ApiController::class, 'nearExpiryBatches']);
});
