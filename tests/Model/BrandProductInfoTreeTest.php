<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\BrandProductInfoTree;
use PHPUnit\Framework\TestCase;

final class BrandProductInfoTreeTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'id' => 'brand-tree-1',
            'feedDate' => null,
            'items' => [],
        ];

        $result = BrandProductInfoTree::fromArray($data);

        self::assertSame('brand-tree-1', $result->id);
        self::assertNull($result->feedDate);
        self::assertSame([], $result->items);
    }

    public function testFromArrayParsesBrandsAndModels(): void
    {
        $data = [
            'id' => null,
            'feedDate' => '2024-01-15T10:00:00+00:00',
            'items' => [
                [
                    'name' => 'Brand X',
                    'models' => [
                        ['name' => 'Model A', 'assetType' => 'laptop'],
                    ],
                ],
            ],
        ];

        $result = BrandProductInfoTree::fromArray($data);

        self::assertCount(1, $result->items);
        self::assertSame('Brand X', $result->items[0]->name);
        self::assertCount(1, $result->items[0]->models);
        self::assertSame('Model A', $result->items[0]->models[0]->name);
        self::assertSame('laptop', $result->items[0]->models[0]->assetType);
    }

    public function testFromArrayHandlesInvalidFeedDate(): void
    {
        $data = [
            'id' => 'tree-1',
            'feedDate' => 'not-a-valid-date',
            'items' => [],
        ];

        $result = BrandProductInfoTree::fromArray($data);

        self::assertNull($result->feedDate);
    }
}
