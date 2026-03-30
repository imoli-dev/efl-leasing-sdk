<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Verification\BlueMediaProcessStateResponse;
use Imoli\EflLeasingSdk\Model\Verification\ResultBlueMedia;
use Imoli\EflLeasingSdk\Model\Verification\StatusBlueMedia;
use PHPUnit\Framework\TestCase;

final class BlueMediaProcessStateResponseTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'transactionId' => 'tx-1',
            'status' => 'OK',
            'result' => 'POSITIVE',
        ];

        $result = BlueMediaProcessStateResponse::fromArray($data);

        self::assertSame('tx-1', $result->getTransactionId());
        self::assertSame(StatusBlueMedia::Ok, $result->getStatus());
        self::assertSame(ResultBlueMedia::Positive, $result->getResult());
    }

    public function testFromArrayHandlesMissingFields(): void
    {
        $data = [];

        $result = BlueMediaProcessStateResponse::fromArray($data);

        self::assertNull($result->getTransactionId());
        self::assertSame(StatusBlueMedia::Error, $result->getStatus());
        self::assertSame(ResultBlueMedia::Negative, $result->getResult());
    }
}
