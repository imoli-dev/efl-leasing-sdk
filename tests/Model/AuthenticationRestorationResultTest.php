<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Restoration\AuthenticationRestorationResult;
use Imoli\EflLeasingSdk\Model\Restoration\ReauthenticationAction;
use PHPUnit\Framework\TestCase;

final class AuthenticationRestorationResultTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'token' => 'jwt-token-123',
            'transactionId' => 'tx-1',
            'action' => 'None',
        ];

        $result = AuthenticationRestorationResult::fromArray($data);

        self::assertSame('jwt-token-123', $result->token);
        self::assertSame('tx-1', $result->transactionId);
        self::assertSame(ReauthenticationAction::None, $result->action);
    }

    public function testFromArrayParsesPaymentPending(): void
    {
        $data = [
            'token' => null,
            'transactionId' => null,
            'action' => 'PaymentPending',
        ];

        $result = AuthenticationRestorationResult::fromArray($data);

        self::assertSame(ReauthenticationAction::PaymentPending, $result->action);
    }
}
