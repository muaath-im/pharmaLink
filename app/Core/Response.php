<?php
/**
 * app/Core/Response.php
 * HTTP Response abstraction with uniform JSON envelopes and security defaults.
 * Phase 05: System Architecture Design
 */

declare(strict_types=1);

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content    = $content;
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
    }

    public static function make(string $content = '', int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, $headers);
    }

    /**
     * Create a standardized JSON response matching the system architecture contract.
     */
    public static function json(
        mixed $data = null,
        int $statusCode = 200,
        string $message = '',
        array $headers = []
    ): self {
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $payload = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data'    => $data,
            'message' => $message,
            'meta'    => [
                'timestamp' => time()
            ]
        ];

        return new self((string)json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $statusCode, $headers);
    }

    /**
     * Create a standardized JSON error response.
     */
    public static function error(
        string $message,
        int $statusCode = 400,
        string $errorCode = 'BAD_REQUEST',
        array $fields = [],
        array $headers = []
    ): self {
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        $payload = [
            'success' => false,
            'error'   => [
                'code'    => $errorCode,
                'message' => $message,
                'fields'  => $fields
            ],
            'meta'    => [
                'timestamp' => time()
            ]
        ];

        return new self((string)json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $statusCode, $headers);
    }

    /**
     * Create an HTTP redirection response.
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        return new self('', $statusCode, ['Location' => $url]);
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function status(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Emit headers and content to client output buffer.
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}", true);
            }
        }

        echo $this->content;
    }
}
