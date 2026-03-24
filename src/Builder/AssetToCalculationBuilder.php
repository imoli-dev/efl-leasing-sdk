<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;

/**
 * Fluent builder for AssetToCalculation model.
 */
final class AssetToCalculationBuilder
{
    private ?string $transactionId = null;

    /** @var OfferItem[] */
    private array $offerItems = [];

    private ?string $returnToBasketUrl = null;

    private ?bool $basketCalculation = null;

    public function withTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function addOfferItem(OfferItem $item): self
    {
        $this->offerItems[] = $item;

        return $this;
    }

    /**
     * @param OfferItem[] $items
     */
    public function withOfferItems(array $items): self
    {
        $this->offerItems = $items;

        return $this;
    }

    public function withReturnToBasketUrl(?string $returnToBasketUrl): self
    {
        $this->returnToBasketUrl = $returnToBasketUrl;

        return $this;
    }

    public function withBasketCalculation(?bool $basketCalculation): self
    {
        $this->basketCalculation = $basketCalculation;

        return $this;
    }

    public function build(): AssetToCalculation
    {
        if ($this->transactionId === null) {
            throw new \LogicException('transactionId is required to build AssetToCalculation');
        }

        if ($this->offerItems === []) {
            throw new \LogicException('At least one offerItem is required to build AssetToCalculation');
        }

        return new AssetToCalculation(
            $this->transactionId,
            $this->offerItems,
            $this->returnToBasketUrl,
            $this->basketCalculation,
        );
    }

    public static function create(string $transactionId): self
    {
        return (new self())->withTransactionId($transactionId);
    }
}
