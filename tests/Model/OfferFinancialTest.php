<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\OfferFinancial;
use PHPUnit\Framework\TestCase;

final class OfferFinancialTest extends TestCase
{
    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [
            'netOfferValue' => 1000.0,
            'netLastRentResidualValue' => 100.0,
        ];

        $result = OfferFinancial::fromArray($data);

        self::assertSame(1000.0, $result->netOfferValue);
        self::assertSame(100.0, $result->netLastRentResidualValue);
        self::assertNull($result->netResidualValuePercent);
        self::assertNull($result->calculatedNetInstallmentValue);
    }

    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'netResidualValuePercent' => 10.5,
            'recommendedPrice' => 1200.0,
            'netResidualValue' => 120.0,
            'netInitialPayment' => 200.0,
            'grossOfferValue' => 1230.0,
            'partnerGrossOfferValue' => 1230.0,
            'grossResidualValue' => 147.6,
            'netInitialResidualValue' => 120.0,
            'netOfferValue' => 1000.0,
            'netLastRentResidualValue' => 100.0,
            'grossResidualValuePercent' => 12.0,
            'grossInitialPayment' => 246.0,
            'pure' => ['netInstallmentAmount' => 25.0],
            'insurance' => null,
            'calculatedNetInstallmentValue' => 25.0,
            'calculatedGrossInstallmentValue' => 30.75,
        ];

        $result = OfferFinancial::fromArray($data);

        self::assertSame(10.5, $result->netResidualValuePercent);
        self::assertSame(1200.0, $result->recommendedPrice);
        self::assertSame(200.0, $result->netInitialPayment);
        self::assertSame(25.0, $result->calculatedNetInstallmentValue);
        self::assertSame(30.75, $result->calculatedGrossInstallmentValue);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\FinancialPure::class, $result->pure);
        self::assertSame(25.0, $result->pure->netInstallmentAmount);
    }

    public function testFromArrayParsesInsurance(): void
    {
        $data = [
            'netOfferValue' => 1000.0,
            'netLastRentResidualValue' => 100.0,
            'insurance' => [
                'netInsuranceInstallmentAmount' => 5.0,
                'grossInsuranceTotalAmount' => 180.0,
            ],
        ];

        $result = OfferFinancial::fromArray($data);

        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\FinancialInsurance::class, $result->insurance);
        self::assertSame(5.0, $result->insurance->netInsuranceInstallmentAmount);
        self::assertSame(180.0, $result->insurance->grossInsuranceTotalAmount);
    }
}
