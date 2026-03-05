<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http;

use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Exception\ApiException;
use Imoli\EflLeasingSdk\Model\Error\ProblemDetails;

/**
 * High-level HTTP helper that knows how to talk to the EFL Leasing Online API.
 *
 * It is responsible for:
 * - Building full URLs from the SDK configuration and relative paths.
 * - Applying authentication headers (ApiKey or Bearer token).
 * - Mapping non-success responses to ApiException.
 */
final class EflHttpClient
{
    private Config $config;

    private HttpClientInterface $httpClient;

    private ?RequestLoggerInterface $logger;

    public function __construct(Config $config, HttpClientInterface $httpClient, ?RequestLoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Performs a request authenticated with the ApiKey scheme.
     *
     * @param non-empty-string $method
     * @param non-empty-string $path Relative API path starting with /lon/...
     * @param array<string, string> $query
     * @param array<string, string> $headers
     */
    public function requestWithApiKey(
        string $method,
        string $path,
        array $query = [],
        array $headers = [],
        ?string $body = null,
    ): HttpResponse {
        $headers['ApiKey'] = $this->config->getApiKey();

        return $this->send($method, $path, $query, $headers, $body);
    }

    /**
     * Performs a request authenticated with the Bearer token scheme.
     *
     * @param non-empty-string $method
     * @param non-empty-string $path Relative API path starting with /lon/...
     * @param array<string, string|array<string>> $query
     * @param array<string, string> $headers
     */
    public function requestWithBearerToken(
        string $method,
        string $path,
        string $token,
        array $query = [],
        array $headers = [],
        ?string $body = null,
    ): HttpResponse {
        $headers['Authorization'] = 'Bearer ' . $token;

        return $this->send($method, $path, $query, $headers, $body);
    }

    /**
     * Performs an unauthenticated request.
     *
     * @param non-empty-string $method
     * @param non-empty-string $path
     * @param array<string, string> $query
     * @param array<string, string> $headers
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        array $headers = [],
        ?string $body = null,
    ): HttpResponse {
        return $this->send($method, $path, $query, $headers, $body);
    }

    /**
     * @param non-empty-string $method
     * @param non-empty-string $path
     * @param array<string, string|array<string>> $query
     * @param array<string, string> $headers
     */
    private function send(
        string $method,
        string $path,
        array $query,
        array $headers,
        ?string $body,
    ): HttpResponse {
        $url = $this->buildUrl($path, $query);

        if ($url === '') {
            throw new \InvalidArgumentException('URL cannot be empty'); // @codeCoverageIgnore
        }

        if ($this->logger !== null) {
            $this->logger->logRequest($method, $url, $headers, $body);
        }

        $response = $this->httpClient->request($method, $url, $headers, $body);

        if ($this->logger !== null) {
            $this->logger->logResponse($response->getStatusCode(), $response->getBody());
        }

        if ($response->getStatusCode() >= 400) {
            throw $this->createApiException($response);
        }

        return $response;
    }

    /**
     * Builds URL with query string. Array values are serialized as repeated params
     * (e.g. statusBPM=a&statusBPM=b) for compatibility with ASP.NET Core style APIs.
     *
     * @param array<string, string|array<string>> $query
     */
    private function buildUrl(string $path, array $query): string
    {
        $base = $this->config->getBaseUrl();
        $path = '/' . ltrim($path, '/');

        $url = $base . $path;

        if ($query !== []) {
            $parts = [];
            foreach ($query as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $parts[] = urlencode((string) $key) . '=' . urlencode((string) $v);
                    }
                } else {
                    $parts[] = urlencode((string) $key) . '=' . urlencode((string) $value);
                }
            }
            $url .= '?' . implode('&', $parts);
        }

        return $url;
    }

    private function createApiException(HttpResponse $response): ApiException
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody();

        $problemDetails = null;
        $messageSuffix = $body;

        try {
            /** @var array<string, mixed>|null $decoded */
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                $problemDetails = ProblemDetails::fromArray($decoded);
                $title = $problemDetails->title;
                $detail = $problemDetails->detail;

                if ($title !== null || $detail !== null) {
                    $parts = array_filter([$title, $detail], static fn (?string $part): bool => $part !== null && $part !== '');
                    $messageSuffix = implode(' - ', $parts);
                }
            }
        } catch (\JsonException) {
            // Keep raw body as message suffix when JSON cannot be parsed.
        }

        $message = sprintf('EFL Leasing API error (HTTP %d)', $statusCode);

        if ($messageSuffix !== '') {
            $message .= ': ' . $messageSuffix;
        }

        return new ApiException($message, $statusCode, $problemDetails);
    }
}
