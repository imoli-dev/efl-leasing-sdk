<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Http\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use Imoli\EflLeasingSdk\Exception\HttpException;
use Imoli\EflLeasingSdk\Http\Adapter\GuzzleHttpAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class GuzzleHttpAdapterTest extends TestCase
{
    public function testRequestReturnsHttpResponse(): void
    {
        $responseBody = '{"ok":true}';
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($responseBody);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeaders')->willReturn(['Content-Type' => ['application/json']]);
        $response->method('getBody')->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
        $client->method('request')
            ->willReturn($response);

        $adapter = new GuzzleHttpAdapter($client);
        $result = $adapter->request('GET', 'https://example.com/api', ['X-Custom' => 'value'], null);

        self::assertSame(200, $result->getStatusCode());
        self::assertSame($responseBody, $result->getBody());
        self::assertArrayHasKey('Content-Type', $result->getHeaders());
    }

    public function testRequestPassesBodyWhenProvided(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('ok');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getHeaders')->willReturn([]);
        $response->method('getBody')->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
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

        $adapter = new GuzzleHttpAdapter($client);
        $adapter->request('POST', 'https://example.com/api', [], '{"data":1}');
    }

    public function testRequestThrowsHttpExceptionOnGuzzleException(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method('request')
            ->willThrowException(new ConnectException('Connection refused', $this->createMock(RequestInterface::class)));

        $adapter = new GuzzleHttpAdapter($client);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('HTTP request failed: Connection refused');

        $adapter->request('GET', 'https://example.com/api');
    }
}
