<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\DemoApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class DemoApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): DemoApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new DemoApiClient(new EflHttpClient($config, $recorder));
    }

    public function testGetIdentityReturnUrlBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getIdentityReturnUrl();

        self::assertSame('GET', $rec->method);
        self::assertSame('https://api.example.test/lon/api/v1/Demo/GetIdentityReturnUrl', $rec->url);
        self::assertArrayNotHasKey('Authorization', $rec->headers);
    }
}
