<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\VerificationResultBuilder;
use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;
use PHPUnit\Framework\TestCase;

final class VerificationResultBuilderTest extends TestCase
{
    public function testBuildReturnsVerificationResult(): void
    {
        $result = VerificationResult::builder()
            ->withStatus('OK')
            ->withResult('POSITIVE')
            ->build();

        self::assertInstanceOf(VerificationResult::class, $result);
        $payload = $result->toRequestPayload();
        self::assertSame('OK', $payload['status']);
        self::assertSame('POSITIVE', $payload['result']);
    }

    public function testBuildWithNullValues(): void
    {
        $result = VerificationResult::builder()->build();

        self::assertInstanceOf(VerificationResult::class, $result);
        self::assertSame([], $result->toRequestPayload());
    }

    public function testCreateShortcutReturnsBuilder(): void
    {
        $builder = VerificationResultBuilder::create();

        self::assertInstanceOf(VerificationResultBuilder::class, $builder);
    }
}
