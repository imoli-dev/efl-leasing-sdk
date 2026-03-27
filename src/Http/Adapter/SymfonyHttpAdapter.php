<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http\Adapter;

use Imoli\EflLeasingSdk\Exception\HttpException;
use Imoli\EflLeasingSdk\Http\HttpClientInterface;
use Imoli\EflLeasingSdk\Http\HttpResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as SymfonyHttpException;

/**
 * HTTP client adapter that delegates to Symfony HttpClient.
 *
 * Requires symfony/http-client. Install with:
 *   composer require symfony/http-client
 */
final class SymfonyHttpAdapter implements HttpClientInterface
{
    public function __construct(
        private readonly SymfonyHttpClientInterface $client,
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
            ];

            if ($body !== null) {
                $options['body'] = $body;
            }

            $response = $this->client->request($method, $url, $options);

            return new HttpResponse(
                $response->getStatusCode(),
                $this->flattenHeaders($response->getHeaders(false)),
                $response->getContent(false),
            );
        } catch (SymfonyHttpException $e) {
            throw new HttpException(
                'HTTP request failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e,
            );
        }
    }

    /**
     * @param array<string, string[]> $headers
     *
     * @return array<string, string>
     */
    private function flattenHeaders(array $headers): array
    {
        $result = [];

        foreach ($headers as $name => $values) {
            $result[$name] = implode(', ', $values);
        }

        return $result;
    }
}
