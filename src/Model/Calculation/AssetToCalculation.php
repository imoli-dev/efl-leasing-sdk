<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

use Imoli\EflLeasingSdk\Builder\AssetToCalculationBuilder;

/**
 * Represents the payload for /Calculation/CalculateBasicOffer.
 */
final class AssetToCalculation
{
    public static function builder(string $transactionId): AssetToCalculationBuilder
    {
        return AssetToCalculationBuilder::create($transactionId);
    }

    /**
     * @var OfferItem[]
     */
    private array $offerItems;

    private string $transactionId;

    private ?string $returnToBasketUrl;

    private ?bool $basketCalculation;

    /**
     * @param OfferItem[] $offerItems
     */
    public function __construct(
        string $transactionId,
        array $offerItems,
        ?string $returnToBasketUrl = null,
        ?bool $basketCalculation = null,
    ) {
        $this->transactionId = $transactionId;
        $this->offerItems = $offerItems;
        $this->returnToBasketUrl = $returnToBasketUrl;
        $this->basketCalculation = $basketCalculation;
    }

    /**
     * @return OfferItem[]
     */
    public function getOfferItems(): array
    {
        return $this->offerItems;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getReturnToBasketUrl(): ?string
    {
        return $this->returnToBasketUrl;
    }

    public function isBasketCalculation(): ?bool
    {
        return $this->basketCalculation;
    }

    /**
     * @return array{
     *     transactionId: string,
     *     returnToBasketUrl?: string|null,
     *     basketCalculation?: bool|null,
     *     offerItems: array<int, array<string, mixed>>
     * }
     */
    public function toRequestPayload(): array
    {
        $items = [];

        foreach ($this->offerItems as $item) {
            $items[] = $item->toRequestPayload();
        }

        $payload = [
            'transactionId' => $this->transactionId,
            'offerItems' => $items,
        ];

        if ($this->returnToBasketUrl !== null) {
            $payload['returnToBasketUrl'] = $this->returnToBasketUrl;
        }

        if ($this->basketCalculation !== null) {
            $payload['basketCalculation'] = $this->basketCalculation;
        }

        return $payload;
    }
}
