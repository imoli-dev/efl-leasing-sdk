<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

/**
 * Insurance-related financial amounts.
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (FinancialInsurance schema)
 */
final class FinancialInsurance
{
    public ?float $netInsuranceInstallmentAmount;

    public ?float $vatInsuranceInstallmentAmount;

    public ?float $grossInsuranceInstallmentAmount;

    public ?float $netInsuranceTotalAmount;

    public ?float $grossInsuranceTotalAmount;

    public function __construct(
        ?float $netInsuranceInstallmentAmount,
        ?float $vatInsuranceInstallmentAmount,
        ?float $grossInsuranceInstallmentAmount,
        ?float $netInsuranceTotalAmount,
        ?float $grossInsuranceTotalAmount,
    ) {
        $this->netInsuranceInstallmentAmount = $netInsuranceInstallmentAmount;
        $this->vatInsuranceInstallmentAmount = $vatInsuranceInstallmentAmount;
        $this->grossInsuranceInstallmentAmount = $grossInsuranceInstallmentAmount;
        $this->netInsuranceTotalAmount = $netInsuranceTotalAmount;
        $this->grossInsuranceTotalAmount = $grossInsuranceTotalAmount;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['netInsuranceInstallmentAmount']) ? (float) $data['netInsuranceInstallmentAmount'] : null,
            isset($data['vatInsuranceInstallmentAmount']) ? (float) $data['vatInsuranceInstallmentAmount'] : null,
            isset($data['grossInsuranceInstallmentAmount']) ? (float) $data['grossInsuranceInstallmentAmount'] : null,
            isset($data['netInsuranceTotalAmount']) ? (float) $data['netInsuranceTotalAmount'] : null,
            isset($data['grossInsuranceTotalAmount']) ? (float) $data['grossInsuranceTotalAmount'] : null,
        );
    }
}
