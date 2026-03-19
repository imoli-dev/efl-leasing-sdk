<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

final class EsbCalculateBasicOfferRestReturn
{
    public ?string $transactionId;

    public ?int $calculationId;

    public ?\DateTimeImmutable $calculationTimestamp;

    /** @var OfferVariant[] */
    public array $variants;

    public bool $basketCalculation;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->transactionId = isset($data['transactionId']) && is_string($data['transactionId'])
            ? $data['transactionId']
            : null;
        $self->calculationId = isset($data['calculationId']) ? (int) $data['calculationId'] : null;

        $self->calculationTimestamp = null;
        if (isset($data['calculationTimestamp']) && is_string($data['calculationTimestamp'])) {
            try {
                $self->calculationTimestamp = new \DateTimeImmutable($data['calculationTimestamp']);
            } catch (\Exception) {
                $self->calculationTimestamp = null;
            }
        }

        $self->variants = [];
        if (isset($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $variant) {
                if (is_array($variant)) {
                    $self->variants[] = OfferVariant::fromArray($variant);
                }
            }
        }

        $self->basketCalculation = (bool) ($data['basketCalculation'] ?? false);

        return $self;
    }

    public static function emptyForTransaction(string $transactionId): self
    {
        $self = new self();
        $self->transactionId = $transactionId;
        $self->calculationId = null;
        $self->calculationTimestamp = null;
        $self->variants = [];
        $self->basketCalculation = false;

        return $self;
    }
}
