<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\AddressBuilder;
use Imoli\EflLeasingSdk\Model\Customer\Address;
use PHPUnit\Framework\TestCase;

final class AddressBuilderTest extends TestCase
{
    public function testBuildReturnsAddress(): void
    {
        $address = Address::builder()
            ->withGuid('addr-guid')
            ->withName('HQ')
            ->withTypeId('registered_office')
            ->withCity('Warsaw')
            ->withStreet('Main St')
            ->withHouseNumber('1')
            ->withPostalCode('00-001')
            ->withCountryCode('PL')
            ->build();

        self::assertInstanceOf(Address::class, $address);
        $payload = $address->toRequestPayload();
        self::assertSame('Warsaw', $payload['city']);
        self::assertSame('registered_office', $payload['type']['id']);
        self::assertSame('registered_office', $payload['type']['name']);
    }

    public function testBuildIncludesFlatNumberWhenSet(): void
    {
        $address = Address::builder()
            ->withGuid('addr-guid')
            ->withName('HQ')
            ->withTypeId('residence')
            ->withCity('Warsaw')
            ->withStreet('Main')
            ->withHouseNumber('1')
            ->withPostalCode('00-001')
            ->withCountryCode('PL')
            ->withFlatNumber('5')
            ->build();

        self::assertSame('5', $address->toRequestPayload()['flatNumber']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $address = AddressBuilder::create('g', 'HQ', 'office', 'Warsaw', 'St', '1', '00-001', 'PL')->build();

        self::assertSame('Warsaw', $address->toRequestPayload()['city']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('guid, name, typeId, city, street, houseNumber, postalCode and countryCode are required');

        Address::builder()->withGuid('g')->withCity('Warsaw')->build();
    }
}
