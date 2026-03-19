<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

final class CalculationData
{
    public EsbProcessStatus $status;

    /** @var array<string, mixed>|null */
    public ?array $basket;

    public ?EsbCalculateBasicOfferRestReturn $calculation;

    public ?int $calculationVariantId;

    public ?PartnerData $partnerData;

    public ?string $returnToBasketUrl;

    public ?string $signProcessRedirectUrl;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();

        $statusValue = is_string($data['status'] ?? null) ? $data['status'] : EsbProcessStatus::Error->value;
        $self->status = EsbProcessStatus::from($statusValue);

        $self->basket = isset($data['basket']) && is_array($data['basket']) ? $data['basket'] : null;

        $self->calculation = isset($data['calculation']) && is_array($data['calculation'])
            ? EsbCalculateBasicOfferRestReturn::fromArray($data['calculation'])
            : null;

        $self->calculationVariantId = isset($data['calculationVariantId']) ? (int) $data['calculationVariantId'] : null;

        $self->partnerData = isset($data['partnerData']) && is_array($data['partnerData'])
            ? PartnerData::fromArray($data['partnerData'])
            : null;

        $self->returnToBasketUrl = isset($data['returnToBasketUrl']) && is_string($data['returnToBasketUrl'])
            ? $data['returnToBasketUrl']
            : null;

        $self->signProcessRedirectUrl = isset($data['signProcessRedirectUrl']) && is_string($data['signProcessRedirectUrl'])
            ? $data['signProcessRedirectUrl']
            : null;

        return $self;
    }
}
