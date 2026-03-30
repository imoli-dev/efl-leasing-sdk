<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;
use PHPUnit\Framework\TestCase;

final class VerificationInitializationParamsTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $params = new VerificationInitializationParams(
            'Jan',
            'Kowalski',
            'Main St',
            '1',
            '00-001',
            'Warsaw',
            'jan@example.com',
        );

        $payload = $params->toRequestPayload();

        self::assertSame('Jan', $payload['firstName']);
        self::assertSame('Kowalski', $payload['lastName']);
        self::assertSame('Main St', $payload['residenceAddressStreet']);
        self::assertSame('1', $payload['residenceAddressHouseNumber']);
        self::assertSame('00-001', $payload['residenceAddressPostalCode']);
        self::assertSame('Warsaw', $payload['residenceAddressCity']);
        self::assertSame('jan@example.com', $payload['email']);
    }
}
