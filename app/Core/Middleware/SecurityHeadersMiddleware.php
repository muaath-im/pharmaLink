<?php
/**
 * app/Core/Middleware/SecurityHeadersMiddleware.php
 * Defense-in-depth HTTP security headers middleware.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;

class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $response = $next($request);

        // Standard OWASP Security Headers
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->header('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy
        $csp = "default-src 'self'; "
             . "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; "
             . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; "
             . "font-src 'self' https://fonts.gstatic.com data:; "
             . "img-src 'self' data: https:; "
             . "connect-src 'self'; "
             . "frame-ancestors 'self'; "
             . "base-uri 'self'; "
             . "form-action 'self'";
        $response->header('Content-Security-Policy', $csp);

        return $response;
    }
}
