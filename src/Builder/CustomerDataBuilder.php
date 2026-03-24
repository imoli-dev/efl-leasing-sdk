<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\Company;
use Imoli\EflLeasingSdk\Model\Customer\CustomerData;

/**
 * Fluent builder for CustomerData model.
 */
final class CustomerDataBuilder
{
    private string $transactionId;

    private int $offerId;

    private ?Company $company = null;

    public function __construct(string $transactionId, int $offerId)
    {
        $this->transactionId = $transactionId;
        $this->offerId = $offerId;
    }

    public function withCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function build(): CustomerData
    {
        if ($this->company === null) {
            throw new \LogicException('Company is required to build CustomerData');
        }

        return new CustomerData(
            $this->transactionId,
            $this->offerId,
            $this->company,
        );
    }
}
