<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\ProductType;
use PHPUnit\Framework\TestCase;

final class ProductTypeTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'name' => 'Laptop',
            'id' => 5,
            'vatRate' => 23.0,
            'recommended' => true,
        ];

        $result = ProductType::fromArray($data);

        self::assertSame('Laptop', $result->name);
        self::assertSame(5, $result->id);
        self::assertSame(23.0, $result->vatRate);
        self::assertTrue($result->recommended);
    }

    public function testFromArrayHandlesMissingFields(): void
    {
        $data = [];

        $result = ProductType::fromArray($data);

        self::assertNull($result->name);
        self::assertSame(0, $result->id);
        self::assertSame(0.0, $result->vatRate);
        self::assertFalse($result->recommended);
    }
}
