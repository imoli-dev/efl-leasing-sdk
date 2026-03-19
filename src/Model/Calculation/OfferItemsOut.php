<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

final class OfferItemsOut
{
    public ?int $count;

    public ?int $assetTypeId;

    public ?string $id;

    public ?AssetOfferFinancial $financing;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->count = isset($data['count']) ? (int) $data['count'] : null;
        $self->assetTypeId = isset($data['assetTypeId']) ? (int) $data['assetTypeId'] : null;
        $self->id = isset($data['id']) && is_string($data['id']) ? $data['id'] : null;
        $self->financing = isset($data['financing']) && is_array($data['financing'])
            ? AssetOfferFinancial::fromArray($data['financing'])
            : null;

        return $self;
    }
}
