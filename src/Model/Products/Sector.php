<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Products;

final class Sector
{
    public ?string $name;

    public int $id;

    /** @var ProductClass[] */
    public array $classes;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : null;
        $self->id = (int) ($data['id'] ?? 0);
        $self->classes = [];

        if (isset($data['classes']) && is_array($data['classes'])) {
            foreach ($data['classes'] as $class) {
                if (is_array($class)) {
                    $self->classes[] = ProductClass::fromArray($class);
                }
            }
        }

        return $self;
    }
}
