<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

final class OfferVariant
{
    public ?int $calculationVariantId;

    public ?int $duration;

    public ?int $payment;

    /** @var OfferItemsOut[] */
    public array $assets;

    public ?OfferFinancial $total;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->calculationVariantId = isset($data['calculationVariantId']) ? (int) $data['calculationVariantId'] : null;
        $self->duration = isset($data['duration']) ? (int) $data['duration'] : null;
        $self->payment = isset($data['payment']) ? (int) $data['payment'] : null;
        $self->assets = [];

        if (isset($data['assets']) && is_array($data['assets'])) {
            foreach ($data['assets'] as $asset) {
                if (is_array($asset)) {
                    $self->assets[] = OfferItemsOut::fromArray($asset);
                }
            }
        }

        $self->total = isset($data['total']) && is_array($data['total'])
            ? OfferFinancial::fromArray($data['total'])
            : null;

        return $self;
    }
}
