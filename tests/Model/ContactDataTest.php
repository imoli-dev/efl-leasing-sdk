<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Lead\ContactData;
use Imoli\EflLeasingSdk\Model\Lead\Prospect;
use Imoli\EflLeasingSdk\Model\Lead\StatementLead;
use PHPUnit\Framework\TestCase;

final class ContactDataTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $prospect = new Prospect(
            'Jan',
            'Kowalski',
            '1234567890',
            '00-001',
            '+48123456789',
            'jan@example.com',
        );
        $statement = new StatementLead(1);

        $contactData = new ContactData($prospect, [$statement]);

        $payload = $contactData->toRequestPayload();

        self::assertSame('Jan', $payload['prospect']['firstName']);
        self::assertCount(1, $payload['statementLead']);
        self::assertSame(1, $payload['statementLead'][0]['statementConfigurationId']);
    }
}
