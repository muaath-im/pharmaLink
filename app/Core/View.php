<?php
/**
 * app/Core/View.php
 * Secure View Rendering Engine with layout nesting and XSS auto-escaping.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    private static string $viewsPath = '';

    public static function setViewsPath(string $path): void
    {
        self::$viewsPath = rtrim($path, '/\\');
    }

    public static function getViewsPath(): string
    {
        if (self::$viewsPath === '') {
            self::$viewsPath = dirname(__DIR__) . '/Views';
        }

        return self::$viewsPath;
    }

    /**
     * Render a view file with optional master layout wrapping.
     *
     * @param string $view Relative view name (e.g. 'home/index' or 'catalog/show')
     * @param array $data Variables passed to the view
     * @param string|null $layout Master layout wrapper (e.g. 'layouts/main')
     * @return string Rendered HTML
     */
    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        $viewFile = self::resolveViewPath($view);

        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewFile}");
        }

        // Render inner view content
        $content = self::renderFile($viewFile, $data);

        // If wrapped in layout, inject into layout's slot
        if ($layout !== null) {
            $layoutFile = self::resolveViewPath($layout);
            if (!file_exists($layoutFile)) {
                throw new RuntimeException("Layout file not found: {$layoutFile}");
            }
            $layoutData = array_merge($data, ['slot' => $content]);
            return self::renderFile($layoutFile, $layoutData);
        }

        return $content;
    }

    /**
     * HTML Escaping helper to mitigate Cross-Site Scripting (XSS).
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function resolveViewPath(string $view): string
    {
        $normalized = str_replace('.', '/', $view);
        return self::getViewsPath() . '/' . trim($normalized, '/') . '.php';
    }

    private static function renderFile(string $filePath, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        try {
            include $filePath;
            return (string)ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }
}
