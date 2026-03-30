<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\StatementLeadBuilder;
use Imoli\EflLeasingSdk\Model\Lead\StatementLead;
use PHPUnit\Framework\TestCase;

final class StatementLeadBuilderTest extends TestCase
{
    public function testBuildReturnsStatementLead(): void
    {
        $stmt = StatementLead::builder()
            ->withStatementConfigurationId(1)
            ->build();

        self::assertInstanceOf(StatementLead::class, $stmt);
        self::assertSame(1, $stmt->toRequestPayload()['statementConfigurationId']);
    }

    public function testBuildWithOptionalFields(): void
    {
        $stmt = StatementLead::builder()
            ->withStatementConfigurationId(1)
            ->withStatementCategoryId(2)
            ->withSelected(true)
            ->build();

        $payload = $stmt->toRequestPayload();
        self::assertSame(2, $payload['statementCategoryId']);
        self::assertTrue($payload['selected']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $stmt = StatementLeadBuilder::create(5)->build();

        self::assertSame(5, $stmt->toRequestPayload()['statementConfigurationId']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('statementConfigurationId is required');

        StatementLead::builder()->build();
    }
}
