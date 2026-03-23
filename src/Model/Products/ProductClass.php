<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Products;

final class ProductClass
{
    public ?string $name;

    /** @var ProductType[] */
    public array $productTypes;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : null;
        $self->productTypes = [];

        if (isset($data['productTypes']) && is_array($data['productTypes'])) {
            foreach ($data['productTypes'] as $productType) {
                if (is_array($productType)) {
                    $self->productTypes[] = ProductType::fromArray($productType);
                }
            }
        }

        return $self;
    }
}
