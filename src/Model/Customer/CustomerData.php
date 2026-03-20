<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\CustomerDataBuilder;

/**
 * Represents the payload for /Customer/PostCustomerDataForLon.
 */
final class CustomerData
{
    public static function builder(string $transactionId, int $offerId): CustomerDataBuilder
    {
        return new CustomerDataBuilder($transactionId, $offerId);
    }

    private string $transactionId;

    private int $offerId;

    private Company $company;

    public function __construct(string $transactionId, int $offerId, Company $company)
    {
        $this->transactionId = $transactionId;
        $this->offerId = $offerId;
        $this->company = $company;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        return [
            'transactionId' => $this->transactionId,
            'offerId' => $this->offerId,
            'company' => $this->company->toRequestPayload(),
        ];
    }
}
