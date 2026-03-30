<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use PHPUnit\Framework\TestCase;

final class OfferItemTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $item = new OfferItem(
            count: 1,
            id: 'ITEM-1',
            vatRate: 23.0,
            itemDetails: [new ItemDetail('name', 'Laptop')],
        );

        $payload = $item->toRequestPayload();

        self::assertSame(1, $payload['count']);
        self::assertSame('ITEM-1', $payload['id']);
        self::assertSame(23.0, $payload['vatRate']);
        self::assertCount(1, $payload['itemDetails']);
        self::assertSame(['id' => 'name', 'value' => 'Laptop'], $payload['itemDetails'][0]);
    }

    public function testToRequestPayloadIncludesOptionalFields(): void
    {
        $item = new OfferItem(
            count: 2,
            id: 'ITEM-2',
            vatRate: 8.0,
            itemDetails: [new ItemDetail('name', 'Table')],
            supplierId: 'SUP-1',
            type: 'equipment',
            category: 'furniture',
            totalAmountNet: 1000.0,
            netValue: 500.0,
            grossValue: 540.0,
        );

        $payload = $item->toRequestPayload();

        self::assertSame('SUP-1', $payload['supplierId']);
        self::assertSame('equipment', $payload['type']);
        self::assertSame('furniture', $payload['category']);
        self::assertSame(1000.0, $payload['totalAmountNet']);
        self::assertSame(500.0, $payload['netValue']);
        self::assertSame(540.0, $payload['grossValue']);
    }
}
