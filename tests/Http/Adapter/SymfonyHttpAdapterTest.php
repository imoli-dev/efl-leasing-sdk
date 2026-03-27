<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Http\Adapter;

use Imoli\EflLeasingSdk\Exception\HttpException;
use Imoli\EflLeasingSdk\Http\Adapter\SymfonyHttpAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as SymfonyHttpException;

final class SymfonyHttpAdapterTest extends TestCase
{
    public function testRequestReturnsHttpResponse(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeaders')->with(false)->willReturn(['content-type' => ['application/json']]);
        $response->method('getContent')->with(false)->willReturn('{"ok":true}');

        $client = $this->createMock(SymfonyHttpClientInterface::class);
        $client->method('request')
            ->willReturn($response);

        $adapter = new SymfonyHttpAdapter($client);
        $result = $adapter->request('GET', 'https://example.com/api', ['X-Custom' => 'value'], null);

        self::assertSame(200, $result->getStatusCode());
        self::assertSame('{"ok":true}', $result->getBody());
        self::assertArrayHasKey('content-type', $result->getHeaders());
    }

    public function testRequestPassesBodyWhenProvided(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeaders')->with(false)->willReturn([]);
        $response->method('getContent')->with(false)->willReturn('ok');

        $client = $this->createMock(SymfonyHttpClientInterface::class);
        $client->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                'https://example.com/api',
                self::callback(function (array $options): bool {
                    return isset($options['body']) && $options['body'] === '{"data":1}';
                })
            )
            ->willReturn($response);

        $adapter = new SymfonyHttpAdapter($client);
        $adapter->request('POST', 'https://example.com/api', [], '{"data":1}');
    }

    public function testRequestThrowsHttpExceptionOnSymfonyException(): void
    {
        $exception = new class ('Connection refused') extends \Exception implements SymfonyHttpException {
        };

        $client = $this->createMock(SymfonyHttpClientInterface::class);
        $client->method('request')
            ->willThrowException($exception);

        $adapter = new SymfonyHttpAdapter($client);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('HTTP request failed: Connection refused');

        $adapter->request('GET', 'https://example.com/api');
    }
}
