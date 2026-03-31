<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use PHPUnit\Framework\TestCase;

final class CustomerDataStatementTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $statement = new CustomerDataStatement('stmt-guid', true, 'gdpr');

        $payload = $statement->toRequestPayload();

        self::assertSame('stmt-guid', $payload['guid']);
        self::assertTrue($payload['agreement']);
        self::assertSame('gdpr', $payload['statementType']['id']);
        self::assertSame('gdpr', $payload['statementType']['name']);
        self::assertSame(['major' => 1, 'minor' => 0, 'patch' => 0], $payload['statementType']['version']);
        self::assertArrayNotHasKey('validFrom', $payload);
    }

    public function testToRequestPayloadIncludesValidFromWhenSet(): void
    {
        $statement = new CustomerDataStatement('stmt-guid', false, 'marketing', '2024-01-01');

        $payload = $statement->toRequestPayload();

        self::assertSame('2024-01-01', $payload['validFrom']);
    }
}
