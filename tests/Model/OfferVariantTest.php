<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\OfferVariant;
use PHPUnit\Framework\TestCase;

final class OfferVariantTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'calculationVariantId' => 1,
            'duration' => 36,
            'payment' => 12,
            'assets' => [],
            'total' => null,
        ];

        $result = OfferVariant::fromArray($data);

        self::assertSame(1, $result->calculationVariantId);
        self::assertSame(36, $result->duration);
        self::assertSame(12, $result->payment);
        self::assertSame([], $result->assets);
        self::assertNull($result->total);
    }

    public function testFromArrayParsesAssetsAndTotal(): void
    {
        $data = [
            'calculationVariantId' => null,
            'duration' => null,
            'payment' => null,
            'assets' => [
                ['count' => 1, 'assetTypeId' => 5, 'id' => 'asset-1', 'financing' => null],
            ],
            'total' => [
                'netOfferValue' => 1000.0,
                'netLastRentResidualValue' => 100.0,
            ],
        ];

        $result = OfferVariant::fromArray($data);

        self::assertCount(1, $result->assets);
        self::assertSame(1, $result->assets[0]->count);
        self::assertSame(5, $result->assets[0]->assetTypeId);
        self::assertSame('asset-1', $result->assets[0]->id);
        self::assertNotNull($result->total);
        self::assertSame(1000.0, $result->total->netOfferValue);
        self::assertSame(100.0, $result->total->netLastRentResidualValue);
    }
}
