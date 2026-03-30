<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;
use Imoli\EflLeasingSdk\Model\Customer\Person;
use PHPUnit\Framework\TestCase;

final class PersonTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'Street', '1', '00-001', 'PL');
        $idDoc = new IdentityDocument('doc-guid', 'ABC123456', 'City Hall Warsaw', '2020-01-15', 'id_card');

        $person = new Person(
            'person-guid',
            'Jan',
            'Kowalski',
            '1234567890',
            '90010112345',
            '1990-01-01',
            'Warsaw',
            false,
            $address,
            'PL',
            [$idDoc],
        );

        $payload = $person->toRequestPayload();

        self::assertSame('person-guid', $payload['guid']);
        self::assertSame('Jan', $payload['firstName']);
        self::assertSame('Kowalski', $payload['lastName']);
        self::assertSame('1234567890', $payload['nip']);
        self::assertSame('90010112345', $payload['pesel']);
        self::assertSame('1990-01-01', $payload['birthDate']);
        self::assertSame('Warsaw', $payload['birthPlace']);
        self::assertFalse($payload['pep']);
        self::assertSame(['id' => 'PL'], $payload['countryOfOrigin']);
        self::assertArrayHasKey('identityDocuments', $payload);
        self::assertCount(1, $payload['identityDocuments']);
        self::assertSame('ABC123456', $payload['identityDocuments'][0]['number']);
        self::assertArrayNotHasKey('middleName', $payload);
        self::assertArrayNotHasKey('statements', $payload);
    }

    public function testToRequestPayloadIncludesOptionalFields(): void
    {
        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'Street', '1', '00-001', 'PL');
        $idDoc = new IdentityDocument('doc-guid', 'ABC123456', 'City Hall', '2020-01-15', 'id_card');
        $statement = new CustomerDataStatement('stmt-guid', true, 'gdpr');

        $person = new Person(
            guid: 'person-guid',
            firstName: 'Jan',
            lastName: 'Kowalski',
            nip: '1234567890',
            pesel: '90010112345',
            birthDate: '1990-01-01',
            birthPlace: 'Warsaw',
            pep: false,
            address: $address,
            countryOfOriginId: 'PL',
            identityDocuments: [$idDoc],
            middleName: 'Adam',
            statements: [$statement],
        );

        $payload = $person->toRequestPayload();

        self::assertSame('Adam', $payload['middleName']);
        self::assertCount(1, $payload['statements']);
    }
}
