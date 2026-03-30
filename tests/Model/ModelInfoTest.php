<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\ModelInfo;
use PHPUnit\Framework\TestCase;

final class ModelInfoTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = ['name' => 'XPS 15', 'assetType' => 'laptop'];

        $result = ModelInfo::fromArray($data);

        self::assertSame('XPS 15', $result->name);
        self::assertSame('laptop', $result->assetType);
    }

    public function testFromArrayHandlesMissingFields(): void
    {
        $data = [];

        $result = ModelInfo::fromArray($data);

        self::assertNull($result->name);
        self::assertNull($result->assetType);
    }
}
