<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

/**
 * Financial data for a single asset in an offer.
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (AssetOfferFinancial schema)
 */
final class AssetOfferFinancial
{
    public ?float $netResidualValuePercent;

    public ?float $recommendedPrice;

    public ?float $netResidualValue;

    public ?float $netInitialPayment;

    public ?float $grossOfferValue;

    public ?float $partnerGrossOfferValue;

    public ?float $grossResidualValue;

    public ?float $netInitialResidualValue;

    public float $netOfferValue;

    public float $netLastRentResidualValue;

    public ?FinancialPure $pure;

    public float $grossResidualValuePercent;

    public ?float $grossInitialPayment;

    public ?FinancialInsurance $insurance;

    public function __construct(
        ?float $netResidualValuePercent,
        ?float $recommendedPrice,
        ?float $netResidualValue,
        ?float $netInitialPayment,
        ?float $grossOfferValue,
        ?float $partnerGrossOfferValue,
        ?float $grossResidualValue,
        ?float $netInitialResidualValue,
        float $netOfferValue,
        float $netLastRentResidualValue,
        ?FinancialPure $pure,
        float $grossResidualValuePercent,
        ?float $grossInitialPayment,
        ?FinancialInsurance $insurance,
    ) {
        $this->netResidualValuePercent = $netResidualValuePercent;
        $this->recommendedPrice = $recommendedPrice;
        $this->netResidualValue = $netResidualValue;
        $this->netInitialPayment = $netInitialPayment;
        $this->grossOfferValue = $grossOfferValue;
        $this->partnerGrossOfferValue = $partnerGrossOfferValue;
        $this->grossResidualValue = $grossResidualValue;
        $this->netInitialResidualValue = $netInitialResidualValue;
        $this->netOfferValue = $netOfferValue;
        $this->netLastRentResidualValue = $netLastRentResidualValue;
        $this->pure = $pure;
        $this->grossResidualValuePercent = $grossResidualValuePercent;
        $this->grossInitialPayment = $grossInitialPayment;
        $this->insurance = $insurance;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['netResidualValuePercent']) ? (float) $data['netResidualValuePercent'] : null,
            isset($data['recommendedPrice']) ? (float) $data['recommendedPrice'] : null,
            isset($data['netResidualValue']) ? (float) $data['netResidualValue'] : null,
            isset($data['netInitialPayment']) ? (float) $data['netInitialPayment'] : null,
            isset($data['grossOfferValue']) ? (float) $data['grossOfferValue'] : null,
            isset($data['partnerGrossOfferValue']) ? (float) $data['partnerGrossOfferValue'] : null,
            isset($data['grossResidualValue']) ? (float) $data['grossResidualValue'] : null,
            isset($data['netInitialResidualValue']) ? (float) $data['netInitialResidualValue'] : null,
            (float) ($data['netOfferValue'] ?? 0.0),
            (float) ($data['netLastRentResidualValue'] ?? 0.0),
            isset($data['pure']) && is_array($data['pure']) ? FinancialPure::fromArray($data['pure']) : null,
            (float) ($data['grossResidualValuePercent'] ?? 0.0),
            isset($data['grossInitialPayment']) ? (float) $data['grossInitialPayment'] : null,
            isset($data['insurance']) && is_array($data['insurance'])
                ? FinancialInsurance::fromArray($data['insurance'])
                : null,
        );
    }
}
