<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\FinancialInsurance;
use PHPUnit\Framework\TestCase;

final class FinancialInsuranceTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'netInsuranceInstallmentAmount' => 10.5,
            'vatInsuranceInstallmentAmount' => 2.42,
            'grossInsuranceInstallmentAmount' => 12.92,
            'netInsuranceTotalAmount' => 378.0,
            'grossInsuranceTotalAmount' => 465.12,
        ];

        $result = FinancialInsurance::fromArray($data);

        self::assertSame(10.5, $result->netInsuranceInstallmentAmount);
        self::assertSame(2.42, $result->vatInsuranceInstallmentAmount);
        self::assertSame(12.92, $result->grossInsuranceInstallmentAmount);
        self::assertSame(378.0, $result->netInsuranceTotalAmount);
        self::assertSame(465.12, $result->grossInsuranceTotalAmount);
    }

    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [];

        $result = FinancialInsurance::fromArray($data);

        self::assertNull($result->netInsuranceInstallmentAmount);
        self::assertNull($result->vatInsuranceInstallmentAmount);
        self::assertNull($result->grossInsuranceInstallmentAmount);
        self::assertNull($result->netInsuranceTotalAmount);
        self::assertNull($result->grossInsuranceTotalAmount);
    }

    public function testFromArrayCastsNumericStringsToFloat(): void
    {
        $data = [
            'netInsuranceInstallmentAmount' => '15.50',
            'grossInsuranceTotalAmount' => '200.00',
        ];

        $result = FinancialInsurance::fromArray($data);

        self::assertSame(15.5, $result->netInsuranceInstallmentAmount);
        self::assertSame(200.0, $result->grossInsuranceTotalAmount);
    }
}
