<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationResult;
use PHPUnit\Framework\TestCase;

final class VerificationInitializationResultTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'status' => 'OK',
            'description' => 'Verification started',
            'redirectUrl' => 'https://example.com/verify',
            'orderUuid' => 'uuid-123',
        ];

        $result = VerificationInitializationResult::fromArray($data);

        self::assertSame('OK', $result->getStatus());
        self::assertSame('Verification started', $result->getDescription());
        self::assertSame('https://example.com/verify', $result->getRedirectUrl());
        self::assertSame('uuid-123', $result->getOrderUuid());
    }

    public function testFromArrayHandlesMissingFields(): void
    {
        $data = [];

        $result = VerificationInitializationResult::fromArray($data);

        self::assertNull($result->getStatus());
        self::assertNull($result->getDescription());
        self::assertNull($result->getRedirectUrl());
        self::assertNull($result->getOrderUuid());
    }
}
