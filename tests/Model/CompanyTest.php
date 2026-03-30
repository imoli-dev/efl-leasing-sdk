<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\Company;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use Imoli\EflLeasingSdk\Model\Customer\Phone;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $address = new Address('addr-guid', 'HQ', 'registered_office', 'Warsaw', 'Main St', '1', '00-001', 'PL');
        $email = new EmailAddress('email-guid', 'contact@example.com', 'work');
        $phone = new Phone('phone-guid', '+48', '123456789', 'mobile', 'mobile');
        $statement = new CustomerDataStatement('stmt-guid', true, 'gdpr');

        $company = new Company(
            'company-guid',
            '1234567890',
            [$email],
            [$phone],
            [],
            [$address],
            [$statement],
        );

        $payload = $company->toRequestPayload();

        self::assertSame('company-guid', $payload['guid']);
        self::assertSame('1234567890', $payload['nip']);
        self::assertCount(1, $payload['emails']);
        self::assertCount(1, $payload['phones']);
        self::assertCount(1, $payload['addresses']);
        self::assertCount(1, $payload['statements']);
        self::assertSame('contact@example.com', $payload['emails'][0]['email']);
        self::assertSame('123456789', $payload['phones'][0]['number']);
    }
}
