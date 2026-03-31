<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\CustomerDataStatementBuilder;
use Imoli\EflLeasingSdk\Model\DescriptorPayload;

final class CustomerDataStatement
{
    public static function builder(): CustomerDataStatementBuilder
    {
        return new CustomerDataStatementBuilder();
    }

    private string $guid;

    private bool $agreement;

    private string $statementTypeId;

    private ?string $validFrom;

    public function __construct(
        string $guid,
        bool $agreement,
        string $statementTypeId,
        ?string $validFrom = null,
    ) {
        $this->guid = $guid;
        $this->agreement = $agreement;
        $this->statementTypeId = $statementTypeId;
        $this->validFrom = $validFrom;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'guid' => $this->guid,
            'agreement' => $this->agreement,
            'statementType' => DescriptorPayload::fromId($this->statementTypeId),
        ];

        if ($this->validFrom !== null) {
            $payload['validFrom'] = $this->validFrom;
        }

        return $payload;
    }
}
