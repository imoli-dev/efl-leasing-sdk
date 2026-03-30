<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;
use PHPUnit\Framework\TestCase;

final class PostVerificationCodeTest extends TestCase
{
    public function testToRequestPayloadReturnsCorrectStructure(): void
    {
        $code = new PostVerificationCode('tx-1', '123456');

        $payload = $code->toRequestPayload();

        self::assertSame('tx-1', $payload['transactionId']);
        self::assertSame('123456', $payload['verificationCode']);
    }
}
