<?php
/**
 * app/Application.php
 * Main Application Orchestrator & Dependency Container.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App;

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\View;

class Application
{
    private Router $router;
    private static ?self $instance = null;

    public function __construct()
    {
        $this->router = new Router();
        self::$instance = $this;

        // Set View Path
        View::setViewsPath(__DIR__ . '/Views');

        // Load Routes
        $this->loadRoutes();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    private function loadRoutes(): void
    {
        $router = $this->router;

        $webRoutes = __DIR__ . '/Routes/web.php';
        if (file_exists($webRoutes)) {
            require $webRoutes;
        }

        $apiRoutes = __DIR__ . '/Routes/api.php';
        if (file_exists($apiRoutes)) {
            require $apiRoutes;
        }
    }

    /**
     * Handle incoming request and dispatch response.
     */
    public function handle(?Request $request = null): Response
    {
        $req = $request ?? Request::capture();
        return $this->router->dispatch($req);
    }

    /**
     * Run the application, sending output to client.
     */
    public function run(?Request $request = null): void
    {
        $response = $this->handle($request);
        $response->send();
    }
}
