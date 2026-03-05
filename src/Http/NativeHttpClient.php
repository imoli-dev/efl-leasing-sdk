<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http;

use Imoli\EflLeasingSdk\Exception\HttpException;

/**
 * Minimal, native PHP implementation of the HTTP client.
 *
 * This implementation is intended as a fallback and for simple integrations.
 * In production systems, consider providing your own implementation based on
 * a dedicated HTTP library.
 */
final class NativeHttpClient implements HttpClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
    ): HttpResponse {
        throw new HttpException('NativeHttpClient is not implemented yet. Please provide your own HttpClientInterface implementation.');
    }
}
