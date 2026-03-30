<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\CompanyBuilder;
use Imoli\EflLeasingSdk\Builder\CustomerDataBuilder;
use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\CustomerData;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use Imoli\EflLeasingSdk\Model\Customer\Phone;
use PHPUnit\Framework\TestCase;

final class CustomerDataBuilderTest extends TestCase
{
    public function testBuildReturnsCustomerData(): void
    {
        $company = (new CompanyBuilder('company-guid', '1234567890'))
            ->addEmail(new EmailAddress('email-guid', 'contact@example.com', 'work'))
            ->addPhone(new Phone('phone-guid', '+48', '123456789', 'mobile', 'mobile'))
            ->addAddress(new Address('addr-guid', 'HQ', 'registered_office', 'Warsaw', 'Main St', '1', '00-001', 'PL'))
            ->addStatement(new CustomerDataStatement('stmt-guid', true, 'gdpr'))
            ->build();

        $customerData = (new CustomerDataBuilder('tx-1', 42))
            ->withCompany($company)
            ->build();

        self::assertInstanceOf(CustomerData::class, $customerData);

        $payload = $customerData->toRequestPayload();
        self::assertSame('tx-1', $payload['transactionId']);
        self::assertSame(42, $payload['offerId']);
    }

    public function testCustomerDataBuilderReturnsBuilderFromModel(): void
    {
        $builder = \Imoli\EflLeasingSdk\Model\Customer\CustomerData::builder('tx-1', 42);

        self::assertInstanceOf(CustomerDataBuilder::class, $builder);
    }

    public function testBuildThrowsWhenCompanyNotSet(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Company is required to build CustomerData');

        (new CustomerDataBuilder('tx-1', 42))->build();
    }
}
