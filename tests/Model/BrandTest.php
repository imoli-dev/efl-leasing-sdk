<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\Brand;
use PHPUnit\Framework\TestCase;

final class BrandTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = ['name' => 'Dell', 'models' => []];

        $result = Brand::fromArray($data);

        self::assertSame('Dell', $result->name);
        self::assertSame([], $result->models);
    }

    public function testFromArrayParsesModels(): void
    {
        $data = [
            'name' => 'Dell',
            'models' => [
                ['name' => 'XPS 15', 'assetType' => 'laptop'],
            ],
        ];

        $result = Brand::fromArray($data);

        self::assertCount(1, $result->models);
        self::assertSame('XPS 15', $result->models[0]->name);
        self::assertSame('laptop', $result->models[0]->assetType);
    }
}
