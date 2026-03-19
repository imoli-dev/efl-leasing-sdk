<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

/**
 * Pure financial breakdown (installments, totals).
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (FinancialPure schema)
 */
final class FinancialPure
{
    public ?float $netInstallmentAmount;

    public ?float $vatInstallmentAmount;

    public ?float $grossInstallmentAmount;

    public ?float $netTotalAmount;

    public ?float $grossTotalAmount;

    public ?float $netResidualInstallmentAmount;

    public function __construct(
        ?float $netInstallmentAmount,
        ?float $vatInstallmentAmount,
        ?float $grossInstallmentAmount,
        ?float $netTotalAmount,
        ?float $grossTotalAmount,
        ?float $netResidualInstallmentAmount,
    ) {
        $this->netInstallmentAmount = $netInstallmentAmount;
        $this->vatInstallmentAmount = $vatInstallmentAmount;
        $this->grossInstallmentAmount = $grossInstallmentAmount;
        $this->netTotalAmount = $netTotalAmount;
        $this->grossTotalAmount = $grossTotalAmount;
        $this->netResidualInstallmentAmount = $netResidualInstallmentAmount;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['netInstallmentAmount']) ? (float) $data['netInstallmentAmount'] : null,
            isset($data['vatInstallmentAmount']) ? (float) $data['vatInstallmentAmount'] : null,
            isset($data['grossInstallmentAmount']) ? (float) $data['grossInstallmentAmount'] : null,
            isset($data['netTotalAmount']) ? (float) $data['netTotalAmount'] : null,
            isset($data['grossTotalAmount']) ? (float) $data['grossTotalAmount'] : null,
            isset($data['netResidualInstallmentAmount']) ? (float) $data['netResidualInstallmentAmount'] : null,
        );
    }
}
