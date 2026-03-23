<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Products;

final class ModelInfo
{
    public ?string $name;

    public ?string $assetType;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : null;
        $self->assetType = isset($data['assetType']) && is_string($data['assetType']) ? $data['assetType'] : null;

        return $self;
    }
}
