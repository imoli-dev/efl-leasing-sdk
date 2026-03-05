<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http;

/**
 * Simple HTTP response value object used by the SDK.
 */
final class HttpResponse
{
    private int $statusCode;

    /**
     * @var array<string, string>
     */
    private array $headers;

    private string $body;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
