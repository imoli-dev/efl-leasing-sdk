<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http;

/**
 * Interface for logging HTTP request and response metadata.
 *
 * Implementations should avoid logging sensitive data (tokens, API keys, etc.).
 */
interface RequestLoggerInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function logRequest(string $method, string $url, array $headers, ?string $body): void;

    public function logResponse(int $statusCode, string $body): void;
}
