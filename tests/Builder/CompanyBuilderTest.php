<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\CompanyBuilder;
use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\Company;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;
use Imoli\EflLeasingSdk\Model\Customer\Person;
use Imoli\EflLeasingSdk\Model\Customer\Phone;
use PHPUnit\Framework\TestCase;

final class CompanyBuilderTest extends TestCase
{
    public function testBuildReturnsCompanyWithAllFields(): void
    {
        $address = new Address('addr-guid', 'HQ', 'registered_office', 'Warsaw', 'Main St', '1', '00-001', 'PL');
        $email = new EmailAddress('email-guid', 'contact@example.com', 'work');
        $phone = new Phone('phone-guid', '+48', '123456789', 'mobile', 'mobile');
        $statement = new CustomerDataStatement('stmt-guid', true, 'gdpr');

        $personAddress = new Address('addr-person', 'HQ', 'residence', 'Warsaw', 'Main St', '1', '00-001', 'PL');
        $idDoc = new IdentityDocument('doc-guid', 'ABC123456', 'City Hall', '2020-01-15', 'id_card');
        $person = new Person(
            'person-guid',
            'Jan',
            'Kowalski',
            '1234567890',
            '12345678901',
            '1990-01-01',
            'Warsaw',
            false,
            $personAddress,
            'PL',
            [$idDoc],
        );
        $company = (new CompanyBuilder('company-guid', '1234567890'))
            ->addEmail($email)
            ->addPhone($phone)
            ->addPerson($person)
            ->addAddress($address)
            ->addStatement($statement)
            ->build();

        self::assertInstanceOf(Company::class, $company);

        $payload = $company->toRequestPayload();
        self::assertSame('company-guid', $payload['guid']);
        self::assertSame('1234567890', $payload['nip']);
        self::assertCount(1, $payload['emails']);
        self::assertCount(1, $payload['phones']);
        self::assertCount(1, $payload['persons']);
        self::assertCount(1, $payload['addresses']);
        self::assertCount(1, $payload['statements']);
    }

    public function testBuildReturnsCompanyWithEmptyCollections(): void
    {
        $company = (new CompanyBuilder('company-guid', '1234567890'))->build();

        $payload = $company->toRequestPayload();
        self::assertSame([], $payload['emails']);
        self::assertSame([], $payload['phones']);
        self::assertSame([], $payload['persons']);
        self::assertSame([], $payload['addresses']);
        self::assertSame([], $payload['statements']);
    }

    public function testCompanyBuilderReturnsBuilderFromModel(): void
    {
        $builder = Company::builder('guid', 'nip');

        self::assertInstanceOf(CompanyBuilder::class, $builder);
        self::assertInstanceOf(Company::class, $builder->build());
    }
}
