<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use PHPUnit\Framework\TestCase;

final class ItemDetailTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $detail = new ItemDetail('name', 'Laptop');

        $payload = $detail->toRequestPayload();

        self::assertSame('name', $payload['id']);
        self::assertSame('Laptop', $payload['value']);
    }
}
