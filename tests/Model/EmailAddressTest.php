<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use PHPUnit\Framework\TestCase;

final class EmailAddressTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $email = new EmailAddress('email-guid', 'contact@example.com', 'work');

        $payload = $email->toRequestPayload();

        self::assertSame('email-guid', $payload['guid']);
        self::assertSame('contact@example.com', $payload['email']);
        self::assertSame(['id' => 'work'], $payload['type']);
    }
}
