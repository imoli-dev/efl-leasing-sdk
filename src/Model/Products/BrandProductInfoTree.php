<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Products;

final class BrandProductInfoTree
{
    public ?string $id;

    public ?\DateTimeImmutable $feedDate;

    /** @var Brand[] */
    public array $items;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->id = isset($data['id']) && is_string($data['id']) ? $data['id'] : null;

        $self->feedDate = null;
        if (isset($data['feedDate']) && is_string($data['feedDate'])) {
            try {
                $self->feedDate = new \DateTimeImmutable($data['feedDate']);
            } catch (\Exception) {
                $self->feedDate = null;
            }
        }

        $self->items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (is_array($item)) {
                    $self->items[] = Brand::fromArray($item);
                }
            }
        }

        return $self;
    }
}
