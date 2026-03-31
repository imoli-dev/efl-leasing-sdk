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
        self::assertSame('00-001', $payload['postal']['id']);
        self::assertSame('00-001', $payload['postal']['name']);
        self::assertSame(['major' => 1, 'minor' => 0, 'patch' => 0], $payload['postal']['version']);
        self::assertSame('PL', $payload['country']['id']);
        self::assertSame('PL', $payload['country']['name']);
        self::assertSame(['major' => 1, 'minor' => 0, 'patch' => 0], $payload['country']['version']);
        self::assertSame('registered_office', $payload['type']['id']);
        self::assertSame('registered_office', $payload['type']['name']);
        self::assertSame(['major' => 1, 'minor' => 0, 'patch' => 0], $payload['type']['version']);
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
