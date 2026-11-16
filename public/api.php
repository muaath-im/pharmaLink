<?php
/**
 * public/api.php
 * Dedicated RESTful API Entry Point.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

$app = \App\Application::getInstance();
$app->run();
