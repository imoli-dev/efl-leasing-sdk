<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\EsbProcessStatus;
use Imoli\EflLeasingSdk\Model\Process\FoicProcessStateResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Imoli\EflLeasingSdk\Model\Process\FoicProcessStateResponse
 */
final class FoicProcessStateResponseTest extends TestCase
{
    public function testFromArrayHandlesNonStringTransactionId(): void
    {
        $data = [
            'transactionId' => 123,
            'status' => 'Kalkulacja',
            'response' => null,
            'warning' => null,
            'statusWasProcessed' => false,
            'processedResponse' => null,
            'processedStatus' => 'Kalkulacja',
        ];

        $result = FoicProcessStateResponse::fromArray($data);

        self::assertNull($result->transactionId);
    }

    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'transactionId' => 'tx-1',
            'status' => 'Kalkulacja',
            'response' => null,
            'warning' => null,
            'statusWasProcessed' => false,
            'processedResponse' => null,
            'processedStatus' => 'Kalkulacja',
        ];

        $result = FoicProcessStateResponse::fromArray($data);

        self::assertSame('tx-1', $result->transactionId);
        self::assertSame(EsbProcessStatus::Kalkulacja, $result->status);
        self::assertFalse($result->statusWasProcessed);
        self::assertSame(EsbProcessStatus::Kalkulacja, $result->processedStatus);
    }

    public function testFromArrayHandlesNonStringStatus(): void
    {
        $data = [
            'transactionId' => 'tx-1',
            'status' => 123,
            'response' => null,
            'warning' => null,
            'statusWasProcessed' => false,
            'processedResponse' => null,
            'processedStatus' => 'Kalkulacja',
        ];

        $result = FoicProcessStateResponse::fromArray($data);

        self::assertSame(EsbProcessStatus::Error, $result->status);
    }

    public function testFromArrayHandlesNonStringProcessedStatus(): void
    {
        $data = [
            'transactionId' => 'tx-1',
            'status' => 'Kalkulacja',
            'response' => null,
            'warning' => null,
            'statusWasProcessed' => false,
            'processedResponse' => null,
            'processedStatus' => null,
        ];

        $result = FoicProcessStateResponse::fromArray($data);

        self::assertSame(EsbProcessStatus::Error, $result->processedStatus);
    }

    public function testFromArrayParsesResponseWarningAndProcessedResponse(): void
    {
        $data = [
            'transactionId' => 'tx-2',
            'status' => 'Dane_kontrahenta',
            'response' => ['key' => 'value'],
            'warning' => ['message' => 'Warning text'],
            'statusWasProcessed' => true,
            'processedResponse' => ['result' => 'ok'],
            'processedStatus' => 'END',
        ];

        $result = FoicProcessStateResponse::fromArray($data);

        self::assertSame('tx-2', $result->transactionId);
        self::assertSame(EsbProcessStatus::DaneKontrahenta, $result->status);
        self::assertSame(['key' => 'value'], $result->response);
        self::assertSame(['message' => 'Warning text'], $result->warning);
        self::assertTrue($result->statusWasProcessed);
        self::assertSame(['result' => 'ok'], $result->processedResponse);
        self::assertSame(EsbProcessStatus::End, $result->processedStatus);
    }
}
