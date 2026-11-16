<?php
/**
 * app/Core/Middleware/CsrfMiddleware.php
 * Synchronizer Token CSRF validation middleware.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    private array $exemptPaths;

    public function __construct(array $exemptPaths = [])
    {
        $this->exemptPaths = $exemptPaths;
    }

    public function handle(Request $request, callable $next): Response
    {
        // Safe read-only HTTP methods are exempt
        if (in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        // Check path exemptions (e.g. external webhooks if any)
        $path = $request->getPath();
        foreach ($this->exemptPaths as $exempt) {
            if ($path === $exempt || str_starts_with($path, rtrim($exempt, '/') . '/')) {
                return $next($request);
            }
        }

        $token = $request->getCsrfToken();

        if (!Session::validateCsrf($token)) {
            if ($request->isJson()) {
                return Response::error('Invalid or missing anti-CSRF token', 403, 'CSRF_TOKEN_MISMATCH');
            }

            return Response::make('<h1>403 Forbidden</h1><p>Invalid or expired security token. Please refresh the page and try again.</p>', 403);
        }

        return $next($request);
    }
}
