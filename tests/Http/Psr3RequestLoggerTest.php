<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Http;

use Imoli\EflLeasingSdk\Http\Psr3RequestLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class Psr3RequestLoggerTest extends TestCase
{
    public function testLogRequestMasksAuthorizationHeader(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('debug')
            ->with(
                'EFL Leasing API request',
                self::callback(function (array $context): bool {
                    return $context['headers']['Authorization'] === '***';
                })
            );

        $requestLogger = new Psr3RequestLogger($logger);
        $requestLogger->logRequest(
            'GET',
            'https://example.com/api',
            ['Authorization' => 'Bearer secret-token'],
            null,
        );
    }

    public function testLogRequestMasksApiKeyHeader(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('debug')
            ->with(
                'EFL Leasing API request',
                self::callback(function (array $context): bool {
                    return $context['headers']['ApiKey'] === '***';
                })
            );

        $requestLogger = new Psr3RequestLogger($logger);
        $requestLogger->logRequest(
            'GET',
            'https://example.com/api',
            ['ApiKey' => 'secret-key'],
            null,
        );
    }

    public function testLogResponseLogsStatusCodeAndBodyLength(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('debug')
            ->with(
                'EFL Leasing API response',
                [
                    'status_code' => 200,
                    'body_length' => 42,
                ]
            );

        $requestLogger = new Psr3RequestLogger($logger);
        $requestLogger->logResponse(200, str_repeat('x', 42));
    }
}
