<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;

/**
 * Fluent builder for OfferItem model.
 */
final class OfferItemBuilder
{
    private ?int $count = null;

    private ?string $id = null;

    private ?float $vatRate = null;

    /** @var ItemDetail[] */
    private array $itemDetails = [];

    private ?string $supplierId = null;

    private ?string $type = null;

    private ?string $category = null;

    private ?float $totalAmountNet = null;

    private ?float $netValue = null;

    private ?float $grossValue = null;

    public function withCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function withId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withVatRate(float $vatRate): self
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function addItemDetail(ItemDetail $detail): self
    {
        $this->itemDetails[] = $detail;

        return $this;
    }

    /**
     * @param ItemDetail[] $details
     */
    public function withItemDetails(array $details): self
    {
        $this->itemDetails = $details;

        return $this;
    }

    public function withSupplierId(?string $supplierId): self
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    public function withType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function withCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function withTotalAmountNet(?float $totalAmountNet): self
    {
        $this->totalAmountNet = $totalAmountNet;

        return $this;
    }

    public function withNetValue(?float $netValue): self
    {
        $this->netValue = $netValue;

        return $this;
    }

    public function withGrossValue(?float $grossValue): self
    {
        $this->grossValue = $grossValue;

        return $this;
    }

    public function build(): OfferItem
    {
        if ($this->count === null || $this->id === null || $this->vatRate === null) {
            throw new \LogicException('count, id and vatRate are required to build OfferItem');
        }

        if ($this->itemDetails === []) {
            throw new \LogicException('At least one itemDetail is required to build OfferItem');
        }

        return new OfferItem(
            $this->count,
            $this->id,
            $this->vatRate,
            $this->itemDetails,
            $this->supplierId,
            $this->type,
            $this->category,
            $this->totalAmountNet,
            $this->netValue,
            $this->grossValue,
        );
    }

    /**
     * @param ItemDetail[] $itemDetails
     */
    public static function create(int $count, string $id, float $vatRate, array $itemDetails): self
    {
        return (new self())
            ->withCount($count)
            ->withId($id)
            ->withVatRate($vatRate)
            ->withItemDetails($itemDetails);
    }
}
