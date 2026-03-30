<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\TestingApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class TestingApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): TestingApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new TestingApiClient(new EflHttpClient($config, $recorder));
    }

    public function testSendItnBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $payload = ['serviceID' => 's1', 'orderID' => 'o1'];

        $client->sendItn($payload, 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertSame('https://api.example.test/lon/api/v1/Testing/SendITN', $rec->url);
        self::assertSame('bearer', $rec->extractedBearerToken);
        self::assertJsonStringEqualsJsonString(json_encode($payload, JSON_THROW_ON_ERROR), (string) $rec->body);
    }
}
