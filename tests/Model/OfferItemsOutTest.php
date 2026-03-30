<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\OfferItemsOut;
use PHPUnit\Framework\TestCase;

final class OfferItemsOutTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [];

        $result = OfferItemsOut::fromArray($data);

        self::assertNull($result->count);
        self::assertNull($result->assetTypeId);
        self::assertNull($result->id);
        self::assertNull($result->financing);
    }

    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'count' => 2,
            'assetTypeId' => 5,
            'id' => 'asset-123',
            'financing' => [
                'netOfferValue' => 1000.0,
                'netLastRentResidualValue' => 100.0,
                'pure' => ['netInstallmentAmount' => 50.0],
            ],
        ];

        $result = OfferItemsOut::fromArray($data);

        self::assertSame(2, $result->count);
        self::assertSame(5, $result->assetTypeId);
        self::assertSame('asset-123', $result->id);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\AssetOfferFinancial::class, $result->financing);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\FinancialPure::class, $result->financing->pure);
        self::assertSame(50.0, $result->financing->pure->netInstallmentAmount);
    }

    public function testFromArrayParsesFinancingWithInsurance(): void
    {
        $data = [
            'financing' => [
                'netOfferValue' => 500.0,
                'netLastRentResidualValue' => 50.0,
                'insurance' => [
                    'netInsuranceInstallmentAmount' => 3.0,
                    'grossInsuranceTotalAmount' => 108.0,
                ],
            ],
        ];

        $result = OfferItemsOut::fromArray($data);

        self::assertNotNull($result->financing);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\FinancialInsurance::class, $result->financing->insurance);
        self::assertSame(3.0, $result->financing->insurance->netInsuranceInstallmentAmount);
        self::assertSame(108.0, $result->financing->insurance->grossInsuranceTotalAmount);
    }
}
