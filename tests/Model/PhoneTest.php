<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\Phone;
use PHPUnit\Framework\TestCase;

final class PhoneTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $phone = new Phone('phone-guid', '+48', '123456789', 'mobile', 'mobile');

        $payload = $phone->toRequestPayload();

        self::assertSame('phone-guid', $payload['guid']);
        self::assertSame('+48', $payload['prefix']);
        self::assertSame('123456789', $payload['number']);
        self::assertSame(['id' => 'mobile'], $payload['type']);
        self::assertSame(['id' => 'mobile'], $payload['kind']);
    }
}
