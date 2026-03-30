<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\EsbCalculateBasicOfferRestReturn;
use PHPUnit\Framework\TestCase;

final class EsbCalculateBasicOfferRestReturnTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'transactionId' => 'tx-1',
            'calculationId' => 42,
            'calculationTimestamp' => null,
            'variants' => [],
            'basketCalculation' => false,
        ];

        $result = EsbCalculateBasicOfferRestReturn::fromArray($data);

        self::assertSame('tx-1', $result->transactionId);
        self::assertSame(42, $result->calculationId);
        self::assertNull($result->calculationTimestamp);
        self::assertSame([], $result->variants);
        self::assertFalse($result->basketCalculation);
    }

    public function testFromArrayParsesVariants(): void
    {
        $data = [
            'transactionId' => null,
            'calculationId' => null,
            'calculationTimestamp' => '2024-01-15T10:00:00+00:00',
            'variants' => [
                [
                    'calculationVariantId' => 1,
                    'duration' => 36,
                    'payment' => 12,
                    'assets' => [],
                    'total' => null,
                ],
            ],
            'basketCalculation' => true,
        ];

        $result = EsbCalculateBasicOfferRestReturn::fromArray($data);

        self::assertCount(1, $result->variants);
        self::assertSame(1, $result->variants[0]->calculationVariantId);
        self::assertSame(36, $result->variants[0]->duration);
        self::assertTrue($result->basketCalculation);
    }

    public function testFromArrayHandlesInvalidCalculationTimestamp(): void
    {
        $data = [
            'transactionId' => 'tx-1',
            'calculationId' => 1,
            'calculationTimestamp' => 'invalid-date',
            'variants' => [],
            'basketCalculation' => false,
        ];

        $result = EsbCalculateBasicOfferRestReturn::fromArray($data);

        self::assertNull($result->calculationTimestamp);
    }

    public function testEmptyForTransactionCreatesEmptyResult(): void
    {
        $result = EsbCalculateBasicOfferRestReturn::emptyForTransaction('tx-1');

        self::assertSame('tx-1', $result->transactionId);
        self::assertNull($result->calculationId);
        self::assertNull($result->calculationTimestamp);
        self::assertSame([], $result->variants);
        self::assertFalse($result->basketCalculation);
    }
}
