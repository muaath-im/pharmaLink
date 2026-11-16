<?php
/**
 * app/Core/Controller.php
 * Abstract Base Controller providing view rendering, JSON contracts, and validation.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Render an HTML view with master layout wrapping.
     */
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/main'): Response
    {
        $html = View::render($view, $data, $layout);
        return Response::make($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    /**
     * Return a standardized JSON success response.
     */
    protected function json(mixed $data = null, int $statusCode = 200, string $message = ''): Response
    {
        return Response::json($data, $statusCode, $message);
    }

    /**
     * Return a standardized JSON error response.
     */
    protected function error(
        string $message,
        int $statusCode = 400,
        string $errorCode = 'BAD_REQUEST',
        array $fields = []
    ): Response {
        return Response::error($message, $statusCode, $errorCode, $fields);
    }

    /**
     * Return an HTTP redirect response.
     */
    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }

    /**
     * Basic input validation utility.
     * Returns an array of validation errors keyed by field.
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && ($value === null || trim((string)$value) === '')) {
                    $errors[$field] = "The {$field} field is required.";
                    break;
                }

                if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "The {$field} field must be a valid email address.";
                    break;
                }

                if (str_starts_with($rule, 'min:') && !empty($value)) {
                    $min = (int)substr($rule, 4);
                    if (strlen((string)$value) < $min) {
                        $errors[$field] = "The {$field} must be at least {$min} characters.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'max:') && !empty($value)) {
                    $max = (int)substr($rule, 4);
                    if (strlen((string)$value) > $max) {
                        $errors[$field] = "The {$field} may not be greater than {$max} characters.";
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
