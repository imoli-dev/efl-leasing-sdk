<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\CustomerDataStatementBuilder;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use PHPUnit\Framework\TestCase;

final class CustomerDataStatementBuilderTest extends TestCase
{
    public function testBuildReturnsCustomerDataStatement(): void
    {
        $stmt = CustomerDataStatement::builder()
            ->withGuid('stmt-guid')
            ->withAgreement(true)
            ->withStatementTypeId('gdpr')
            ->build();

        self::assertInstanceOf(CustomerDataStatement::class, $stmt);
        self::assertTrue($stmt->toRequestPayload()['agreement']);
    }

    public function testBuildIncludesValidFromWhenSet(): void
    {
        $stmt = CustomerDataStatement::builder()
            ->withGuid('stmt-guid')
            ->withAgreement(true)
            ->withStatementTypeId('gdpr')
            ->withValidFrom('2024-01-01')
            ->build();

        self::assertSame('2024-01-01', $stmt->toRequestPayload()['validFrom']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $stmt = CustomerDataStatementBuilder::create('g', true, 'gdpr')->build();

        self::assertSame('gdpr', $stmt->toRequestPayload()['statementType']['id']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('guid, agreement and statementTypeId are required');

        CustomerDataStatement::builder()->withGuid('g')->build();
    }
}
