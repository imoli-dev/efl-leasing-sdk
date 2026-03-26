<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Imoli\EflLeasingSdk\Exception\HttpException;
use Imoli\EflLeasingSdk\Http\HttpClientInterface;
use Imoli\EflLeasingSdk\Http\HttpResponse;

/**
 * HTTP client adapter that delegates to Guzzle.
 *
 * Requires guzzlehttp/guzzle (^7.0). Install with:
 *   composer require guzzlehttp/guzzle
 */
final class GuzzleHttpAdapter implements HttpClientInterface
{
    public function __construct(
        private readonly ClientInterface $client,
    ) {
    }

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
    ): HttpResponse {
        try {
            $options = [
                'headers' => $headers,
                'http_errors' => false,
            ];

            if ($body !== null) {
                $options['body'] = $body;
            }

            $response = $this->client->request($method, $url, $options);

            $responseHeaders = [];
            foreach ($response->getHeaders() as $name => $values) {
                $responseHeaders[$name] = implode(', ', $values);
            }

            $responseBody = $response->getBody()->getContents();

            return new HttpResponse(
                $response->getStatusCode(),
                $responseHeaders,
                $responseBody,
            );
        } catch (GuzzleException $e) {
            throw new HttpException(
                'HTTP request failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e,
            );
        }
    }
}
