<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Lead\StatementLead;
use PHPUnit\Framework\TestCase;

final class StatementLeadTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $statement = new StatementLead(1);

        $payload = $statement->toRequestPayload();

        self::assertSame(1, $payload['statementConfigurationId']);
        self::assertArrayNotHasKey('statementCategoryId', $payload);
        self::assertArrayNotHasKey('selected', $payload);
    }

    public function testToRequestPayloadIncludesOptionalFields(): void
    {
        $statement = new StatementLead(1, 5, true);

        $payload = $statement->toRequestPayload();

        self::assertSame(5, $payload['statementCategoryId']);
        self::assertTrue($payload['selected']);
    }
}
