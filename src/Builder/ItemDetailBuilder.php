<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;

/**
 * Fluent builder for ItemDetail model.
 */
final class ItemDetailBuilder
{
    private ?string $id = null;

    private ?string $value = null;

    public function withId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function build(): ItemDetail
    {
        if ($this->id === null || $this->value === null) {
            throw new \LogicException('id and value are required to build ItemDetail');
        }

        return new ItemDetail($this->id, $this->value);
    }

    public static function create(string $id, string $value): self
    {
        return (new self())->withId($id)->withValue($value);
    }
}
