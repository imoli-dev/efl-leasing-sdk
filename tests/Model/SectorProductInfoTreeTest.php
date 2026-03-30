<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\SectorProductInfoTree;
use PHPUnit\Framework\TestCase;

final class SectorProductInfoTreeTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'id' => 'tree-1',
            'feedDate' => null,
            'items' => [],
        ];

        $result = SectorProductInfoTree::fromArray($data);

        self::assertSame('tree-1', $result->id);
        self::assertNull($result->feedDate);
        self::assertSame([], $result->items);
    }

    public function testFromArrayParsesSectors(): void
    {
        $data = [
            'id' => null,
            'feedDate' => '2024-01-15T10:00:00+00:00',
            'items' => [
                [
                    'name' => 'Sector A',
                    'id' => 1,
                    'classes' => [
                        [
                            'name' => 'Class 1',
                            'productTypes' => [
                                ['name' => 'Type 1', 'id' => 10, 'vatRate' => 23.0, 'recommended' => true],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = SectorProductInfoTree::fromArray($data);

        self::assertCount(1, $result->items);
        self::assertSame('Sector A', $result->items[0]->name);
        self::assertSame(1, $result->items[0]->id);
        self::assertCount(1, $result->items[0]->classes);
        self::assertSame('Class 1', $result->items[0]->classes[0]->name);
        self::assertCount(1, $result->items[0]->classes[0]->productTypes);
        self::assertSame('Type 1', $result->items[0]->classes[0]->productTypes[0]->name);
        self::assertSame(23.0, $result->items[0]->classes[0]->productTypes[0]->vatRate);
    }

    public function testFromArrayHandlesInvalidFeedDate(): void
    {
        $data = [
            'id' => 'tree-1',
            'feedDate' => 'invalid',
            'items' => [],
        ];

        $result = SectorProductInfoTree::fromArray($data);

        self::assertNull($result->feedDate);
    }
}
