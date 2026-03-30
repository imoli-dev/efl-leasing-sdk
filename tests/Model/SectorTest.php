<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Products\Sector;
use PHPUnit\Framework\TestCase;

final class SectorTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = ['name' => 'IT', 'id' => 1, 'classes' => []];

        $result = Sector::fromArray($data);

        self::assertSame('IT', $result->name);
        self::assertSame(1, $result->id);
        self::assertSame([], $result->classes);
    }

    public function testFromArrayParsesClasses(): void
    {
        $data = [
            'name' => 'IT',
            'id' => 1,
            'classes' => [
                ['name' => 'Computers', 'productTypes' => []],
            ],
        ];

        $result = Sector::fromArray($data);

        self::assertCount(1, $result->classes);
        self::assertSame('Computers', $result->classes[0]->name);
    }
}
