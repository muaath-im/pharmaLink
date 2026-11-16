<?php
/**
 * app/Core/Request.php
 * Immutable HTTP Request abstraction and input sanitization layer.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $queryParams;
    private array $postData;
    private array $serverData;
    private array $headers;
    private ?array $jsonData = null;

    public function __construct(
        ?string $method = null,
        ?string $uri = null,
        ?array $queryParams = null,
        ?array $postData = null,
        ?array $serverData = null
    ) {
        $this->serverData  = $serverData ?? $_SERVER;
        $this->method      = strtoupper($method ?? ($this->serverData['REQUEST_METHOD'] ?? 'GET'));
        $this->uri         = $uri ?? ($this->serverData['REQUEST_URI'] ?? '/');
        $this->path        = (string)parse_url($this->uri, PHP_URL_PATH);
        $this->queryParams = $queryParams ?? $_GET;
        $this->postData    = $postData ?? $_POST;
        $this->headers     = $this->parseHeaders();
    }

    /**
     * Capture the current global HTTP request.
     */
    public static function capture(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    /**
     * Retrieve a query parameter by key with an optional default.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Retrieve a POST parameter by key with an optional default.
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->postData[$key] ?? $default;
    }

    /**
     * Retrieve an input parameter from POST, JSON, or GET.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->isJson()) {
            $json = $this->json();
            if (isset($json[$key])) {
                return $json[$key];
            }
        }

        return $this->postData[$key] ?? $this->queryParams[$key] ?? $default;
    }

    /**
     * Retrieve all input data merged.
     */
    public function all(): array
    {
        if ($this->isJson()) {
            return array_merge($this->queryParams, $this->json());
        }

        return array_merge($this->queryParams, $this->postData);
    }

    /**
     * Determine if request contains JSON content.
     */
    public function isJson(): bool
    {
        $contentType = $this->getHeader('Content-Type') ?? '';
        return str_contains(strtolower($contentType), 'application/json');
    }

    /**
     * Parse and retrieve JSON body.
     */
    public function json(): array
    {
        if ($this->jsonData === null) {
            $raw = (string)file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $this->jsonData = is_array($decoded) ? $decoded : [];
        }

        return $this->jsonData;
    }

    /**
     * Retrieve a specific request header.
     */
    public function getHeader(string $name): ?string
    {
        $normalized = strtolower($name);
        return $this->headers[$normalized] ?? null;
    }

    /**
     * Retrieve all request headers normalized to lowercase.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Extract the CSRF token from header or post body.
     */
    public function getCsrfToken(): ?string
    {
        return $this->getHeader('X-CSRF-TOKEN')
            ?? (string)($this->postData['csrf_token'] ?? $this->json()['csrf_token'] ?? null);
    }

    /**
     * Retrieve client IP address.
     */
    public function getClientIp(): string
    {
        return (string)($this->serverData['HTTP_X_FORWARDED_FOR']
            ?? $this->serverData['REMOTE_ADDR']
            ?? '127.0.0.1');
    }

    /**
     * Retrieve User Agent string.
     */
    public function getUserAgent(): string
    {
        return (string)($this->serverData['HTTP_USER_AGENT'] ?? 'Unknown');
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->serverData as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerName] = (string)$value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $headerName = strtolower(str_replace('_', '-', $key));
                $headers[$headerName] = (string)$value;
            }
        }
        return $headers;
    }
}
