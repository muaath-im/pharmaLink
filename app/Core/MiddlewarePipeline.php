<?php
/**
 * app/Core/MiddlewarePipeline.php
 * Sequential request/response pipeline runner (Onion Architecture).
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core;

use App\Core\Middleware\MiddlewareInterface;

class MiddlewarePipeline
{
    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Run the request through all piped middlewares terminating at destination.
     *
     * @param Request $request
     * @param callable(Request): Response $destination Final handler
     * @return Response
     */
    public function process(Request $request, callable $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function (callable $next, MiddlewareInterface $middleware) {
                return function (Request $req) use ($middleware, $next): Response {
                    return $middleware->handle($req, $next);
                };
            },
            $destination
        );

        return $pipeline($request);
    }
}
