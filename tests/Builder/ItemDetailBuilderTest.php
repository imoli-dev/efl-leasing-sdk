<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\ItemDetailBuilder;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use PHPUnit\Framework\TestCase;

final class ItemDetailBuilderTest extends TestCase
{
    public function testBuildReturnsItemDetail(): void
    {
        $detail = ItemDetail::builder()
            ->withId('name')
            ->withValue('Laptop')
            ->build();

        self::assertInstanceOf(ItemDetail::class, $detail);
        self::assertSame(['id' => 'name', 'value' => 'Laptop'], $detail->toRequestPayload());
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $detail = ItemDetailBuilder::create('category', 'Electronics')->build();

        self::assertSame('category', $detail->toRequestPayload()['id']);
        self::assertSame('Electronics', $detail->toRequestPayload()['value']);
    }

    public function testBuildThrowsWhenIdMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('id and value are required');

        ItemDetail::builder()->withValue('x')->build();
    }

    public function testBuildThrowsWhenValueMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('id and value are required');

        ItemDetail::builder()->withId('x')->build();
    }
}
