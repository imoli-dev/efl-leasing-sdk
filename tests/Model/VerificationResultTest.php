<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;
use PHPUnit\Framework\TestCase;

final class VerificationResultTest extends TestCase
{
    public function testToRequestPayloadWithFullData(): void
    {
        $result = new VerificationResult('OK', 'POSITIVE');

        $payload = $result->toRequestPayload();

        self::assertSame('OK', $payload['status']);
        self::assertSame('POSITIVE', $payload['result']);
    }

    public function testToRequestPayloadWithNullValues(): void
    {
        $result = new VerificationResult();

        $payload = $result->toRequestPayload();

        self::assertEmpty($payload);
    }

    public function testToRequestPayloadWithPartialData(): void
    {
        $result = new VerificationResult('PENDING', null);

        $payload = $result->toRequestPayload();

        self::assertSame('PENDING', $payload['status']);
        self::assertArrayNotHasKey('result', $payload);
    }
}
