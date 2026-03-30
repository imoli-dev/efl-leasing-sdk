<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\ProductsApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ProductsApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): ProductsApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new ProductsApiClient(new EflHttpClient($config, $recorder));
    }

    public function testGetSectorClassAndTypeBuildsCorrectUrl(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getSectorClassAndType('tx-1', 'bearer');

        self::assertSame('GET', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Products/GetSectorClassAndType?transactionId=tx-1',
            $rec->url
        );
    }

    public function testGetBrandModelByProductTypeIdAndPartnerGuidBuildsCorrectUrl(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getBrandModelByProductTypeIdAndPartnerGuid('tx-1', 42, 'bearer');

        self::assertSame('GET', $rec->method);
        self::assertStringContainsString('/lon/api/v1/Products/GetBrandModelByProductTypeIdAndPartnerGuid', $rec->url);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
        self::assertStringContainsString('productTypeId=42', $rec->url);
    }
}
