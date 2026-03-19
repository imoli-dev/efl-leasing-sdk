<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

use Imoli\EflLeasingSdk\Builder\ItemDetailBuilder;

/**
 * Additional metadata for an offer item.
 */
final class ItemDetail
{
    public static function builder(): ItemDetailBuilder
    {
        return new ItemDetailBuilder();
    }

    private string $id;

    private string $value;

    public function __construct(string $id, string $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * @return array{id: string, value: string}
     */
    public function toRequestPayload(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
        ];
    }
}
