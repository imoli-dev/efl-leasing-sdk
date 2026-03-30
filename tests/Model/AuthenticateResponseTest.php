<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Process\AuthenticateResponse;
use PHPUnit\Framework\TestCase;

final class AuthenticateResponseTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'partnerId' => 'partner-123',
            'token' => 'jwt-token-xyz',
        ];

        $result = AuthenticateResponse::fromArray($data);

        self::assertSame('partner-123', $result->partnerId);
        self::assertSame('jwt-token-xyz', $result->token);
    }

    public function testFromArrayWithNullValues(): void
    {
        $data = [
            'partnerId' => null,
            'token' => null,
        ];

        $result = AuthenticateResponse::fromArray($data);

        self::assertNull($result->partnerId);
        self::assertNull($result->token);
    }

    public function testFromArrayWithMissingKeys(): void
    {
        $result = AuthenticateResponse::fromArray([]);

        self::assertNull($result->partnerId);
        self::assertNull($result->token);
    }
}
