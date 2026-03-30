<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\ProductClass;
use PHPUnit\Framework\TestCase;

final class ProductClassTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = ['name' => 'Laptops', 'productTypes' => []];

        $result = ProductClass::fromArray($data);

        self::assertSame('Laptops', $result->name);
        self::assertSame([], $result->productTypes);
    }

    public function testFromArrayParsesProductTypes(): void
    {
        $data = [
            'name' => 'Laptops',
            'productTypes' => [
                ['name' => 'Business', 'id' => 10, 'vatRate' => 23.0, 'recommended' => true],
            ],
        ];

        $result = ProductClass::fromArray($data);

        self::assertCount(1, $result->productTypes);
        self::assertSame('Business', $result->productTypes[0]->name);
        self::assertSame(10, $result->productTypes[0]->id);
    }
}
