<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\EmailAddressBuilder;
use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use PHPUnit\Framework\TestCase;

final class EmailAddressBuilderTest extends TestCase
{
    public function testBuildReturnsEmailAddress(): void
    {
        $email = EmailAddress::builder()
            ->withGuid('email-guid')
            ->withEmail('test@example.com')
            ->withTypeId('work')
            ->build();

        self::assertInstanceOf(EmailAddress::class, $email);
        self::assertSame('test@example.com', $email->toRequestPayload()['email']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $email = EmailAddressBuilder::create('g', 'a@b.com', 'work')->build();

        self::assertSame('a@b.com', $email->toRequestPayload()['email']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('guid, email and typeId are required');

        EmailAddress::builder()->withGuid('g')->build();
    }
}
