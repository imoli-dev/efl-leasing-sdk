<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use PHPUnit\Framework\TestCase;

final class AssetToCalculationTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $item = new OfferItem(1, 'ITEM-1', 23.0, [new ItemDetail('name', 'Laptop')]);
        $basket = new AssetToCalculation('tx-1', [$item]);

        $payload = $basket->toRequestPayload();

        self::assertSame('tx-1', $payload['transactionId']);
        self::assertCount(1, $payload['offerItems']);
        self::assertArrayNotHasKey('returnToBasketUrl', $payload);
        self::assertArrayNotHasKey('basketCalculation', $payload);
    }

    public function testToRequestPayloadIncludesOptionalFields(): void
    {
        $item = new OfferItem(1, 'ITEM-1', 23.0, [new ItemDetail('name', 'Laptop')]);
        $basket = new AssetToCalculation(
            'tx-2',
            [$item],
            'https://example.com/return',
            true,
        );

        $payload = $basket->toRequestPayload();

        self::assertArrayHasKey('returnToBasketUrl', $payload);
        self::assertArrayHasKey('basketCalculation', $payload);
        $returnUrl = $payload['returnToBasketUrl'] ?? null;
        $basketCalc = $payload['basketCalculation'] ?? null;
        self::assertSame('https://example.com/return', $returnUrl);
        self::assertTrue($basketCalc);
    }

    public function testGettersReturnCorrectValues(): void
    {
        $item = new OfferItem(1, 'ITEM-1', 23.0, [new ItemDetail('name', 'Laptop')]);
        $basket = new AssetToCalculation('tx-1', [$item], 'https://example.com', false);

        self::assertSame('tx-1', $basket->getTransactionId());
        self::assertSame([$item], $basket->getOfferItems());
        self::assertSame('https://example.com', $basket->getReturnToBasketUrl());
        self::assertFalse($basket->isBasketCalculation());
    }
}
