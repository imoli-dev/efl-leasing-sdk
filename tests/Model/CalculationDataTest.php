<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\CalculationData;
use Imoli\EflLeasingSdk\Model\Calculation\EsbProcessStatus;
use PHPUnit\Framework\TestCase;

final class CalculationDataTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'status' => 'Kalkulacja',
            'basket' => null,
            'calculation' => null,
            'calculationVariantId' => null,
            'partnerData' => null,
            'returnToBasketUrl' => null,
            'signProcessRedirectUrl' => null,
        ];

        $result = CalculationData::fromArray($data);

        self::assertSame(EsbProcessStatus::Kalkulacja, $result->status);
        self::assertNull($result->basket);
        self::assertNull($result->calculation);
        self::assertNull($result->returnToBasketUrl);
    }

    public function testFromArrayParsesCalculation(): void
    {
        $data = [
            'status' => 'Dane_kontrahenta',
            'basket' => ['transactionId' => 'tx-1'],
            'calculation' => [
                'transactionId' => 'tx-1',
                'calculationId' => 10,
                'variants' => [],
                'basketCalculation' => false,
            ],
            'calculationVariantId' => 5,
            'partnerData' => null,
            'returnToBasketUrl' => 'https://example.com/return',
            'signProcessRedirectUrl' => null,
        ];

        $result = CalculationData::fromArray($data);

        self::assertSame(EsbProcessStatus::DaneKontrahenta, $result->status);
        self::assertNotNull($result->calculation);
        self::assertSame(10, $result->calculation->calculationId);
        self::assertSame(5, $result->calculationVariantId);
        self::assertSame('https://example.com/return', $result->returnToBasketUrl);
    }

    public function testFromArrayParsesPartnerData(): void
    {
        $data = [
            'status' => 'Kalkulacja',
            'basket' => null,
            'calculation' => null,
            'calculationVariantId' => null,
            'partnerData' => [
                'returnToShopUrl' => 'https://shop.example.com/back',
                'returnButtonLabel' => 'Powrót',
            ],
            'returnToBasketUrl' => null,
            'signProcessRedirectUrl' => 'https://sign.example.com',
        ];

        $result = CalculationData::fromArray($data);

        self::assertNotNull($result->partnerData);
        self::assertSame('https://shop.example.com/back', $result->partnerData->returnToShopUrl);
        self::assertSame('Powrót', $result->partnerData->returnButtonLabel);
        self::assertSame('https://sign.example.com', $result->signProcessRedirectUrl);
    }
}
