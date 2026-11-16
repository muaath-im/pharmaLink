<?php
/**
 * app/Core/Router.php
 * Parameter-aware HTTP Router with route groups and middleware attachments.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core;

use App\Core\Middleware\MiddlewareInterface;
use Throwable;

class Router
{
    private array $routes = [];
    private array $groupStack = [];
    /** @var callable|null */
    private $notFoundHandler = null;

    public function get(string $path, callable|array|string $action, array $middlewares = []): self
    {
        return $this->addRoute('GET', $path, $action, $middlewares);
    }

    public function post(string $path, callable|array|string $action, array $middlewares = []): self
    {
        return $this->addRoute('POST', $path, $action, $middlewares);
    }

    public function put(string $path, callable|array|string $action, array $middlewares = []): self
    {
        return $this->addRoute('PUT', $path, $action, $middlewares);
    }

    public function delete(string $path, callable|array|string $action, array $middlewares = []): self
    {
        return $this->addRoute('DELETE', $path, $action, $middlewares);
    }

    public function group(string $prefix, array $middlewares, callable $callback): void
    {
        $this->groupStack[] = [
            'prefix'      => '/' . trim($prefix, '/'),
            'middlewares' => $middlewares
        ];

        $callback($this);

        array_pop($this->groupStack);
    }

    public function setNotFoundHandler(callable $handler): self
    {
        $this->notFoundHandler = $handler;
        return $this;
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path   = '/' . trim($request->getPath(), '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY);

                // Build route-specific middleware pipeline
                $pipeline = new MiddlewarePipeline();
                foreach ($route['middlewares'] as $mw) {
                    if (is_string($mw)) {
                        $mw = new $mw();
                    }
                    if ($mw instanceof MiddlewareInterface) {
                        $pipeline->pipe($mw);
                    }
                }

                $destination = function (Request $req) use ($route, $params): Response {
                    return $this->executeAction($route['action'], $req, $params);
                };

                return $pipeline->process($request, $destination);
            }
        }

        // 404 Route Not Found
        if ($this->notFoundHandler !== null) {
            return ($this->notFoundHandler)($request);
        }

        if ($request->isJson()) {
            return Response::error('Resource not found', 404, 'NOT_FOUND');
        }

        return Response::make('<h1>404 Not Found</h1><p>The requested page does not exist on PharmaLink.</p>', 404);
    }

    private function addRoute(string $method, string $path, callable|array|string $action, array $middlewares = []): self
    {
        $fullPath = '';
        $groupMiddlewares = [];

        foreach ($this->groupStack as $group) {
            $fullPath .= $group['prefix'];
            $groupMiddlewares = array_merge($groupMiddlewares, $group['middlewares']);
        }

        $fullPath .= '/' . trim($path, '/');
        $fullPath = '/' . trim($fullPath, '/');

        // Convert {param} placeholders into named regex patterns
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $fullPath);
        $regex   = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'      => $method,
            'path'        => $fullPath,
            'regex'       => $regex,
            'action'      => $action,
            'middlewares' => array_merge($groupMiddlewares, $middlewares)
        ];

        return $this;
    }

    private function executeAction(callable|array|string $action, Request $request, array $params): Response
    {
        try {
            if (is_callable($action)) {
                $result = $action($request, ...array_values($params));
            } elseif (is_array($action) && count($action) === 2) {
                [$class, $method] = $action;
                $controller = is_string($class) ? new $class() : $class;
                $result = $controller->$method($request, ...array_values($params));
            } else {
                throw new \InvalidArgumentException("Invalid route action specified.");
            }

            if ($result instanceof Response) {
                return $result;
            }

            if (is_array($result) || is_object($result)) {
                return Response::json($result);
            }

            return Response::make((string)$result, 200);
        } catch (Throwable $e) {
            if ($request->isJson()) {
                return Response::error($e->getMessage(), 500, 'INTERNAL_SERVER_ERROR');
            }
            return Response::make("<h1>500 Internal Error</h1><p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>", 500);
        }
    }
}
