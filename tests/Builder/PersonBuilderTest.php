<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;
use Imoli\EflLeasingSdk\Model\Customer\Person;
use PHPUnit\Framework\TestCase;

final class PersonBuilderTest extends TestCase
{
    public function testBuildReturnsPerson(): void
    {
        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'St', '1', '00-001', 'PL');
        $idDoc = new IdentityDocument('doc-guid', 'ABC123', 'City Hall', '2020-01-15', 'id_card');

        $person = Person::builder()
            ->withGuid('person-guid')
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withNip('1234567890')
            ->withPesel('90010112345')
            ->withBirthDate('1990-01-01')
            ->withBirthPlace('Warsaw')
            ->withPep(false)
            ->withAddress($address)
            ->withCountryOfOriginId('PL')
            ->addIdentityDocument($idDoc)
            ->build();

        self::assertInstanceOf(Person::class, $person);
        $payload = $person->toRequestPayload();
        self::assertSame('Jan', $payload['firstName']);
        self::assertCount(1, $payload['identityDocuments']);
    }

    public function testBuildWithOptionalFields(): void
    {
        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'St', '1', '00-001', 'PL');
        $idDoc = new IdentityDocument('doc-guid', 'ABC123', 'City Hall', '2020-01-15', 'id_card');
        $stmt = new CustomerDataStatement('stmt-guid', true, 'gdpr');

        $person = Person::builder()
            ->withGuid('person-guid')
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withNip('1234567890')
            ->withPesel('90010112345')
            ->withBirthDate('1990-01-01')
            ->withBirthPlace('Warsaw')
            ->withPep(false)
            ->withAddress($address)
            ->withCountryOfOriginId('PL')
            ->withIdentityDocuments([$idDoc])
            ->withMiddleName('Adam')
            ->addStatement($stmt)
            ->build();

        $payload = $person->toRequestPayload();
        self::assertSame('Adam', $payload['middleName']);
        self::assertCount(1, $payload['statements']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('guid, firstName, lastName, nip, pesel, birthDate, birthPlace, pep, address and countryOfOriginId');

        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'St', '1', '00-001', 'PL');
        Person::builder()
            ->withGuid('g')
            ->withAddress($address)
            ->build();
    }

    public function testBuildThrowsWhenIdentityDocumentsEmpty(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('At least one identityDocument is required');

        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'St', '1', '00-001', 'PL');
        Person::builder()
            ->withGuid('g')
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withNip('1234567890')
            ->withPesel('90010112345')
            ->withBirthDate('1990-01-01')
            ->withBirthPlace('Warsaw')
            ->withPep(false)
            ->withAddress($address)
            ->withCountryOfOriginId('PL')
            ->build();
    }

    public function testBuildWithStatements(): void
    {
        $address = new Address('addr-guid', 'Residence', 'residence', 'Warsaw', 'St', '1', '00-001', 'PL');
        $idDoc = new IdentityDocument('doc-guid', 'ABC123', 'City Hall', '2020-01-15', 'id_card');
        $stmt1 = new CustomerDataStatement('stmt-1', true, 'gdpr');
        $stmt2 = new CustomerDataStatement('stmt-2', true, 'marketing');

        $person = Person::builder()
            ->withGuid('person-guid')
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withNip('1234567890')
            ->withPesel('90010112345')
            ->withBirthDate('1990-01-01')
            ->withBirthPlace('Warsaw')
            ->withPep(false)
            ->withAddress($address)
            ->withCountryOfOriginId('PL')
            ->addIdentityDocument($idDoc)
            ->withStatements([$stmt1, $stmt2])
            ->build();

        self::assertCount(2, $person->toRequestPayload()['statements']);
    }
}
