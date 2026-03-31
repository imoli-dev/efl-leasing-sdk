<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\DescriptorPayload;
use PHPUnit\Framework\TestCase;

final class DescriptorPayloadTest extends TestCase
{
    public function testFromIdReturnsCompleteDescriptor(): void
    {
        $descriptor = DescriptorPayload::fromId('1');

        self::assertSame('1', $descriptor['id']);
        self::assertSame('1', $descriptor['name']);
        self::assertSame(['major' => 1, 'minor' => 0, 'patch' => 0], $descriptor['version']);
    }
}
