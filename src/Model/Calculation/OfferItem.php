<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

use Imoli\EflLeasingSdk\Builder\OfferItemBuilder;

/**
 * Represents a single item in the leasing offer basket.
 */
final class OfferItem
{
    public static function builder(): OfferItemBuilder
    {
        return new OfferItemBuilder();
    }

    private int $count;

    private string $id;

    private ?string $supplierId;

    private ?string $type;

    private ?string $category;

    private ?float $totalAmountNet;

    private float $vatRate;

    private ?float $netValue;

    private ?float $grossValue;

    /**
     * @var ItemDetail[]
     */
    private array $itemDetails;

    /**
     * @param ItemDetail[] $itemDetails
     */
    public function __construct(
        int $count,
        string $id,
        float $vatRate,
        array $itemDetails,
        ?string $supplierId = null,
        ?string $type = null,
        ?string $category = null,
        ?float $totalAmountNet = null,
        ?float $netValue = null,
        ?float $grossValue = null,
    ) {
        $this->count = $count;
        $this->id = $id;
        $this->vatRate = $vatRate;
        $this->itemDetails = $itemDetails;
        $this->supplierId = $supplierId;
        $this->type = $type;
        $this->category = $category;
        $this->totalAmountNet = $totalAmountNet;
        $this->netValue = $netValue;
        $this->grossValue = $grossValue;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $details = [];

        foreach ($this->itemDetails as $detail) {
            $details[] = $detail->toRequestPayload();
        }

        $payload = [
            'count' => $this->count,
            'id' => $this->id,
            'vatRate' => $this->vatRate,
            'itemDetails' => $details,
        ];

        if ($this->supplierId !== null) {
            $payload['supplierId'] = $this->supplierId;
        }

        if ($this->type !== null) {
            $payload['type'] = $this->type;
        }

        if ($this->category !== null) {
            $payload['category'] = $this->category;
        }

        if ($this->totalAmountNet !== null) {
            $payload['totalAmountNet'] = round($this->totalAmountNet, 4);
        }

        if ($this->netValue !== null) {
            $payload['netValue'] = round($this->netValue, 4);
        }

        if ($this->grossValue !== null) {
            $payload['grossValue'] = round($this->grossValue, 4);
        }

        return $payload;
    }
}
