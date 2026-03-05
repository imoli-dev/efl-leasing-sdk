<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http;

use Imoli\EflLeasingSdk\Exception\HttpException;

/**
 * Abstraction over HTTP client implementation.
 *
 * This interface allows the SDK to remain decoupled from any particular
 * HTTP library while still enabling integrators to plug in their own
 * client implementation (e.g. Guzzle, Symfony HTTP client).
 */
interface HttpClientInterface
{
    /**
     * Sends an HTTP request to the given URL.
     *
     * @param non-empty-string $method HTTP method, e.g. GET, POST.
     * @param non-empty-string $url Absolute URL including scheme and host.
     * @param array<string, string> $headers
     * @param string|null $body
     *
     * @return HttpResponse
     *
     * @throws HttpException When the request fails at transport level.
     */
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
    ): HttpResponse;
}
