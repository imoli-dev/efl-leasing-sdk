<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Http;

use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Exception\ApiException;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpClientInterface;
use Imoli\EflLeasingSdk\Http\HttpResponse;
use PHPUnit\Framework\TestCase;

final class EflHttpClientTest extends TestCase
{
    public function testRequestWithBearerTokenAddsAuthorizationHeader(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public string $method;
            public string $url;
            /** @var array<string, string> */
            public array $headers;

            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                $this->method = $method;
                $this->url = $url;
                $this->headers = $headers;

                return new HttpResponse(200, [], '');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $client->requestWithBearerToken('GET', '/lon/api/v1/Process/Init', 'my-bearer-token');

        self::assertSame('GET', $innerClient->method);
        self::assertSame('https://example.test/lon/api/v1/Process/Init', $innerClient->url);
        self::assertArrayHasKey('Authorization', $innerClient->headers);
        self::assertSame('Bearer my-bearer-token', $innerClient->headers['Authorization']);
    }

    public function testRequestWithApiKeyAddsHeaderAndBuildsUrl(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public string $method;

            public string $url;

            /** @var array<string, string> */
            public array $headers;

            public ?string $body;

            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                $this->method = $method;
                $this->url = $url;
                $this->headers = $headers;
                $this->body = $body;

                return new HttpResponse(200, [], '');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $client->requestWithApiKey(
            'GET',
            '/lon/api/v1/Process/GetToken',
            ['partnerId' => '123'],
        );

        self::assertSame('GET', $innerClient->method);
        self::assertSame('https://example.test/lon/api/v1/Process/GetToken?partnerId=123', $innerClient->url);
        self::assertArrayHasKey('ApiKey', $innerClient->headers);
        self::assertSame('test-key', $innerClient->headers['ApiKey']);
    }

    public function testNonSuccessResponseThrowsApiExceptionWithProblemDetails(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                $problem = [
                    'title' => 'Bad Request',
                    'detail' => 'Something went wrong',
                    'status' => 400,
                ];

                return new HttpResponse(400, ['Content-Type' => 'application/json'], json_encode($problem, JSON_THROW_ON_ERROR));
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Bad Request - Something went wrong');

        try {
            $client->request(
                'GET',
                '/lon/api/v1/Process/Init',
            );
        } catch (ApiException $exception) {
            $details = $exception->getProblemDetails();
            self::assertNotNull($details);
            self::assertSame(400, $details->status);

            throw $exception;
        }
    }

    public function test401ResponseThrowsApiException(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                return new HttpResponse(401, [], 'Unauthorized');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('401');

        $client->request('GET', '/lon/api/v1/Process/Init');
    }

    public function test500ResponseThrowsApiException(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                return new HttpResponse(500, [], 'Internal Server Error');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('500');

        $client->request('GET', '/lon/api/v1/Process/Init');
    }

    public function testNonJsonErrorBodyStillThrowsApiExceptionWithRawMessage(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                return new HttpResponse(503, [], 'Service Unavailable - plain text');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('503');
        $this->expectExceptionMessage('Service Unavailable');

        $client->request('GET', '/lon/api/v1/Process/Init');
    }

    public function testMalformedJsonErrorBodyDoesNotBreakException(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                return new HttpResponse(400, ['Content-Type' => 'application/json'], '{invalid json');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('400');

        $client->request('GET', '/lon/api/v1/Process/Init');
    }

    public function testValidJsonNonArrayErrorBodyUsesRawBodyInMessage(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                return new HttpResponse(400, ['Content-Type' => 'application/json'], 'null');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('400');
        $this->expectExceptionMessage('null');

        $client->request('GET', '/lon/api/v1/Process/Init');
    }

    public function testLoggerIsInvokedWhenProvided(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $loggerState = new class () implements \Imoli\EflLeasingSdk\Http\RequestLoggerInterface {
            public bool $requestLogged = false;
            public bool $responseLogged = false;

            public function logRequest(string $method, string $url, array $headers, ?string $body): void
            {
                $this->requestLogged = true;
            }

            public function logResponse(int $statusCode, string $body): void
            {
                $this->responseLogged = true;
            }
        };

        $innerClient = new class () implements HttpClientInterface {
            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                return new HttpResponse(200, [], 'ok');
            }
        };

        $client = new EflHttpClient($config, $innerClient, $loggerState);

        $client->request('GET', '/lon/api/v1/Process/Init');

        self::assertTrue($loggerState->requestLogged);
        self::assertTrue($loggerState->responseLogged);
    }

    public function testArrayQueryParamsAreSerializedAsRepeatedKeys(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        $innerClient = new class () implements HttpClientInterface {
            public string $url = '';

            public function request(
                string $method,
                string $url,
                array $headers = [],
                ?string $body = null,
            ): HttpResponse {
                $this->url = $url;

                return new HttpResponse(200, [], '');
            }
        };

        $client = new EflHttpClient($config, $innerClient);

        $client->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Process/GetChanges',
            'token',
            [
                'transactionId' => 'tx-1',
                'statusBPM' => ['StatusA', 'StatusB'],
            ],
        );

        self::assertStringContainsString('transactionId=tx-1', $innerClient->url);
        self::assertStringContainsString('statusBPM=StatusA', $innerClient->url);
        self::assertStringContainsString('statusBPM=StatusB', $innerClient->url);
        self::assertStringNotContainsString('statusBPM%5B0%5D', $innerClient->url);
    }
}
