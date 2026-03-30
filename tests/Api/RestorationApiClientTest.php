<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\RestorationApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class RestorationApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): RestorationApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new RestorationApiClient(new EflHttpClient($config, $recorder));
    }

    public function testRestoreCustomerSessionBuildsCorrectUrl(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->restoreCustomerSession('order-123');

        self::assertSame('GET', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Restoration/RestoreCustomerSession?payByLinkOrderId=order-123',
            $rec->url
        );
        self::assertArrayNotHasKey('Authorization', $rec->headers);
    }

    public function testRestoreSessionAfterSigningBuildsCorrectUrl(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->restoreSessionAfterSigning('tx-1');

        self::assertSame('GET', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Restoration/RestoreSessionAfterSigning?transactionId=tx-1',
            $rec->url
        );
    }
}
