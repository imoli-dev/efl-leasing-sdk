<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\OfferItemBuilder;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use PHPUnit\Framework\TestCase;

final class OfferItemBuilderTest extends TestCase
{
    public function testBuildReturnsOfferItem(): void
    {
        $detail = new ItemDetail('name', 'Laptop');
        $item = OfferItem::builder()
            ->withCount(1)
            ->withId('item-1')
            ->withVatRate(23.0)
            ->addItemDetail($detail)
            ->build();

        self::assertInstanceOf(OfferItem::class, $item);
        $payload = $item->toRequestPayload();
        self::assertSame(1, $payload['count']);
        self::assertSame('item-1', $payload['id']);
        self::assertCount(1, $payload['itemDetails']);
    }

    public function testBuildWithOptionalFields(): void
    {
        $item = OfferItem::builder()
            ->withCount(2)
            ->withId('item-1')
            ->withVatRate(23.0)
            ->withItemDetails([new ItemDetail('name', 'X')])
            ->withSupplierId('sup-1')
            ->withNetValue(100.0)
            ->build();

        $payload = $item->toRequestPayload();
        self::assertSame('sup-1', $payload['supplierId']);
        self::assertSame(100.0, $payload['netValue']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $item = OfferItemBuilder::create(1, 'id', 23.0, [new ItemDetail('x', 'y')])->build();

        self::assertSame(1, $item->toRequestPayload()['count']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('count, id and vatRate are required');

        OfferItem::builder()->withCount(1)->build();
    }

    public function testBuildThrowsWhenItemDetailsEmpty(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('At least one itemDetail is required');

        OfferItem::builder()
            ->withCount(1)
            ->withId('id')
            ->withVatRate(23.0)
            ->build();
    }

    public function testBuildWithAllOptionalFields(): void
    {
        $item = OfferItem::builder()
            ->withCount(1)
            ->withId('item-1')
            ->withVatRate(23.0)
            ->addItemDetail(new ItemDetail('name', 'Laptop'))
            ->withSupplierId('sup-1')
            ->withType('electronics')
            ->withCategory('computers')
            ->withTotalAmountNet(1000.0)
            ->withNetValue(1000.0)
            ->withGrossValue(1230.0)
            ->build();

        $payload = $item->toRequestPayload();
        self::assertSame('sup-1', $payload['supplierId']);
        self::assertSame('electronics', $payload['type']);
        self::assertSame('computers', $payload['category']);
        self::assertSame(1000.0, $payload['totalAmountNet']);
        self::assertSame(1000.0, $payload['netValue']);
        self::assertSame(1230.0, $payload['grossValue']);
    }
}
