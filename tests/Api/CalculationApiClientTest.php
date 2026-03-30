<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\CalculationApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class CalculationApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): CalculationApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new CalculationApiClient(new EflHttpClient($config, $recorder));
    }

    public function testCalculateBasicOfferBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $basket = new AssetToCalculation('tx-1', [
            new OfferItem(1, 'item-1', 23.0, [new ItemDetail('name', 'Laptop')]),
        ]);

        $client->calculateBasicOffer($basket, 'bearer-tok');

        self::assertSame('POST', $rec->method);
        self::assertStringContainsString('/lon/api/v1/Calculation/CalculateBasicOffer', $rec->url);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
        self::assertSame('bearer-tok', $rec->extractedBearerToken);
        self::assertJsonStringEqualsJsonString(
            json_encode($basket->toRequestPayload(), JSON_THROW_ON_ERROR),
            (string) $rec->body
        );
    }

    public function testGetBaseDataBuildsCorrectUrl(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getBaseData('tx-1', 'bearer');

        self::assertSame('GET', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Calculation/GetBaseData?transactionId=tx-1',
            $rec->url
        );
    }

    public function testAcceptCalculationBuildsCorrectRequestWithBasketCalculation(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->acceptCalculation('tx-1', 10, 20, true, 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
        self::assertStringContainsString('basketCalculation=true', $rec->url);
        self::assertJsonStringEqualsJsonString(
            '{"calculationId":10,"calculationVariantId":20}',
            (string) $rec->body
        );
    }
}
