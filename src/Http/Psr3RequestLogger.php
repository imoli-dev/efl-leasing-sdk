<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Http;

use Psr\Log\LoggerInterface;

/**
 * PSR-3 adapter for RequestLoggerInterface.
 *
 * Logs request/response metadata at debug level. Masks Authorization and ApiKey headers.
 */
final class Psr3RequestLogger implements RequestLoggerInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, string> $headers
     */
    public function logRequest(string $method, string $url, array $headers, ?string $body): void
    {
        $maskedHeaders = $this->maskSensitiveHeaders($headers);

        $this->logger->debug('EFL Leasing API request', [
            'method' => $method,
            'url' => $url,
            'headers' => $maskedHeaders,
            'body_length' => $body !== null ? strlen($body) : 0,
        ]);
    }

    public function logResponse(int $statusCode, string $body): void
    {
        $this->logger->debug('EFL Leasing API response', [
            'status_code' => $statusCode,
            'body_length' => strlen($body),
        ]);
    }

    /**
     * @param array<string, string> $headers
     *
     * @return array<string, string>
     */
    private function maskSensitiveHeaders(array $headers): array
    {
        $masked = $headers;
        $sensitive = ['authorization', 'apikey'];

        foreach ($masked as $name => $value) {
            if (in_array(strtolower($name), $sensitive, true)) {
                $masked[$name] = '***';
            }
        }

        return $masked;
    }
}
