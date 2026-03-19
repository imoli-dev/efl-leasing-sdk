<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Calculation;

/**
 * Partner data returned in CalculationData.
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (PartnerData schema)
 */
final class PartnerData
{
    public ?string $returnToShopUrl;

    public ?string $returnButtonLabel;

    public function __construct(?string $returnToShopUrl, ?string $returnButtonLabel)
    {
        $this->returnToShopUrl = $returnToShopUrl;
        $this->returnButtonLabel = $returnButtonLabel;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['returnToShopUrl']) && is_string($data['returnToShopUrl']) ? $data['returnToShopUrl'] : null,
            isset($data['returnButtonLabel']) && is_string($data['returnButtonLabel']) ? $data['returnButtonLabel'] : null,
        );
    }
}
