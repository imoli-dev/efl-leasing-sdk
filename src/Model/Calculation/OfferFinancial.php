<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

final class OfferFinancial
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

    public ?float $grossResidualValuePercent;

    public ?float $grossInitialPayment;

    public ?FinancialPure $pure;

    public ?FinancialInsurance $insurance;

    public ?float $calculatedNetInstallmentValue;

    public ?float $calculatedGrossInstallmentValue;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->netResidualValuePercent = isset($data['netResidualValuePercent']) ? (float) $data['netResidualValuePercent'] : null;
        $self->recommendedPrice = isset($data['recommendedPrice']) ? (float) $data['recommendedPrice'] : null;
        $self->netResidualValue = isset($data['netResidualValue']) ? (float) $data['netResidualValue'] : null;
        $self->netInitialPayment = isset($data['netInitialPayment']) ? (float) $data['netInitialPayment'] : null;
        $self->grossOfferValue = isset($data['grossOfferValue']) ? (float) $data['grossOfferValue'] : null;
        $self->partnerGrossOfferValue = isset($data['partnerGrossOfferValue']) ? (float) $data['partnerGrossOfferValue'] : null;
        $self->grossResidualValue = isset($data['grossResidualValue']) ? (float) $data['grossResidualValue'] : null;
        $self->netInitialResidualValue = isset($data['netInitialResidualValue']) ? (float) $data['netInitialResidualValue'] : null;
        $self->netOfferValue = (float) ($data['netOfferValue'] ?? 0.0);
        $self->netLastRentResidualValue = (float) ($data['netLastRentResidualValue'] ?? 0.0);
        $self->grossResidualValuePercent = isset($data['grossResidualValuePercent']) ? (float) $data['grossResidualValuePercent'] : null;
        $self->grossInitialPayment = isset($data['grossInitialPayment']) ? (float) $data['grossInitialPayment'] : null;
        $self->pure = isset($data['pure']) && is_array($data['pure']) ? FinancialPure::fromArray($data['pure']) : null;
        $self->insurance = isset($data['insurance']) && is_array($data['insurance'])
            ? FinancialInsurance::fromArray($data['insurance'])
            : null;
        $self->calculatedNetInstallmentValue = isset($data['calculatedNetInstallmentValue']) ? (float) $data['calculatedNetInstallmentValue'] : null;
        $self->calculatedGrossInstallmentValue = isset($data['calculatedGrossInstallmentValue']) ? (float) $data['calculatedGrossInstallmentValue'] : null;

        return $self;
    }
}
