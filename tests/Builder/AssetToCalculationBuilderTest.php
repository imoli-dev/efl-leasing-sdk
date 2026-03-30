<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\AssetToCalculationBuilder;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use PHPUnit\Framework\TestCase;

final class AssetToCalculationBuilderTest extends TestCase
{
    public function testBuildReturnsAssetToCalculation(): void
    {
        $offerItem = new OfferItem(1, 'item-1', 23.0, [new ItemDetail('name', 'Laptop')]);

        $basket = AssetToCalculation::builder('tx-1')
            ->addOfferItem($offerItem)
            ->build();

        self::assertInstanceOf(AssetToCalculation::class, $basket);
        $payload = $basket->toRequestPayload();
        self::assertSame('tx-1', $payload['transactionId']);
        self::assertCount(1, $payload['offerItems']);
    }

    public function testBuildWithOptionalFields(): void
    {
        $offerItem = new OfferItem(1, 'item-1', 23.0, [new ItemDetail('name', 'Laptop')]);

        $basket = AssetToCalculation::builder('tx-1')
            ->addOfferItem($offerItem)
            ->withReturnToBasketUrl('https://shop.com/basket')
            ->withBasketCalculation(true)
            ->build();

        $payload = $basket->toRequestPayload();
        self::assertSame('https://shop.com/basket', $payload['returnToBasketUrl']);
        self::assertTrue($payload['basketCalculation']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $offerItem = new OfferItem(1, 'item-1', 23.0, [new ItemDetail('name', 'Laptop')]);
        $basket = AssetToCalculationBuilder::create('tx-1')->addOfferItem($offerItem)->build();

        self::assertSame('tx-1', $basket->getTransactionId());
    }

    public function testBuildThrowsWhenTransactionIdMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('transactionId is required');

        (new AssetToCalculationBuilder())
            ->addOfferItem(new OfferItem(1, 'id', 23.0, [new ItemDetail('x', 'y')]))
            ->build();
    }

    public function testBuildThrowsWhenOfferItemsEmpty(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('At least one offerItem is required');

        AssetToCalculation::builder('tx-1')->build();
    }

    public function testWithOfferItemsSetsMultipleItems(): void
    {
        $item1 = new OfferItem(1, 'item-1', 23.0, [new ItemDetail('name', 'A')]);
        $item2 = new OfferItem(2, 'item-2', 8.0, [new ItemDetail('name', 'B')]);

        $basket = AssetToCalculation::builder('tx-1')
            ->withOfferItems([$item1, $item2])
            ->build();

        self::assertCount(2, $basket->getOfferItems());
    }
}
