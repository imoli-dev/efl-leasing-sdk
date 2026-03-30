<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Lead\Prospect;
use PHPUnit\Framework\TestCase;

final class ProspectTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $prospect = new Prospect(
            'Jan',
            'Kowalski',
            '1234567890',
            '00-001',
            '+48123456789',
            'jan@example.com',
        );

        $payload = $prospect->toRequestPayload();

        self::assertSame('Jan', $payload['firstName']);
        self::assertSame('Kowalski', $payload['lastName']);
        self::assertSame('1234567890', $payload['nip']);
        self::assertSame('00-001', $payload['postal']);
        self::assertSame('+48123456789', $payload['phoneNo']);
        self::assertSame('jan@example.com', $payload['email']);
        self::assertArrayNotHasKey('description', $payload);
    }

    public function testToRequestPayloadIncludesDescriptionWhenSet(): void
    {
        $prospect = new Prospect(
            'Jan',
            'Kowalski',
            '1234567890',
            '00-001',
            '+48123456789',
            'jan@example.com',
            'Interested in leasing',
        );

        $payload = $prospect->toRequestPayload();

        self::assertSame('Interested in leasing', $payload['description']);
    }
}
