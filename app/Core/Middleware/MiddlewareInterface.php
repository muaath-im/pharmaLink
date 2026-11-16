<?php
/**
 * app/Core/Middleware/MiddlewareInterface.php
 * Standard contract for HTTP request interceptors.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;

interface MiddlewareInterface
{
    /**
     * Process an incoming request and return an HTTP response.
     *
     * @param Request $request
     * @param callable(Request): Response $next Next handler in pipeline
     * @return Response
     */
    public function handle(Request $request, callable $next): Response;
}
