<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Products;

final class ProductType
{
    public ?string $name;

    public int $id;

    public float $vatRate;

    public bool $recommended;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : null;
        $self->id = (int) ($data['id'] ?? 0);
        $self->vatRate = (float) ($data['vatRate'] ?? 0.0);
        $self->recommended = (bool) ($data['recommended'] ?? false);

        return $self;
    }
}
