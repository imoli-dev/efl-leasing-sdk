<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\Address;
use PHPUnit\Framework\TestCase;

final class AddressTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $address = new Address(
            'addr-guid',
            'Headquarters',
            'registered_office',
            'Warsaw',
            'Main St',
            '1',
            '00-001',
            'PL',
        );

        $payload = $address->toRequestPayload();

        self::assertSame('addr-guid', $payload['guid']);
        self::assertSame('Headquarters', $payload['name']);
        self::assertSame('Warsaw', $payload['city']);
        self::assertSame('Main St', $payload['street']);
        self::assertSame('1', $payload['houseNumber']);
        self::assertSame(['id' => '00-001'], $payload['postal']);
        self::assertSame(['id' => 'PL'], $payload['country']);
        self::assertSame(['id' => 'registered_office'], $payload['type']);
        self::assertArrayNotHasKey('flatNumber', $payload);
    }

    public function testToRequestPayloadIncludesFlatNumberWhenSet(): void
    {
        $address = new Address(
            'addr-guid',
            'Office',
            'correspondence',
            'Krakow',
            'Second St',
            '10',
            '30-001',
            'PL',
            '5',
        );

        $payload = $address->toRequestPayload();

        self::assertSame('5', $payload['flatNumber']);
    }
}
