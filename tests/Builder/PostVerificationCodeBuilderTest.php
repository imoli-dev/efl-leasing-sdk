<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\PostVerificationCodeBuilder;
use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;
use PHPUnit\Framework\TestCase;

final class PostVerificationCodeBuilderTest extends TestCase
{
    public function testBuildReturnsPostVerificationCode(): void
    {
        $code = PostVerificationCode::builder()
            ->withTransactionId('tx-1')
            ->withVerificationCode('123456')
            ->build();

        self::assertInstanceOf(PostVerificationCode::class, $code);
        $payload = $code->toRequestPayload();
        self::assertSame('tx-1', $payload['transactionId']);
        self::assertSame('123456', $payload['verificationCode']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $code = PostVerificationCodeBuilder::create('tx-1', '123456')->build();

        self::assertSame('123456', $code->toRequestPayload()['verificationCode']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('transactionId and verificationCode are required');

        PostVerificationCode::builder()->withTransactionId('tx-1')->build();
    }
}
