<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Products;

final class Brand
{
    public ?string $name;

    /** @var ModelInfo[] */
    public array $models;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : null;
        $self->models = [];

        if (isset($data['models']) && is_array($data['models'])) {
            foreach ($data['models'] as $model) {
                if (is_array($model)) {
                    $self->models[] = ModelInfo::fromArray($model);
                }
            }
        }

        return $self;
    }
}
