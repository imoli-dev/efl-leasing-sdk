<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\IdentityDocumentBuilder;
use Imoli\EflLeasingSdk\Model\DescriptorPayload;

/**
 * Represents an identity document (e.g. ID card, passport) required by the EFL API.
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (IdentityDocument schema)
 */
final class IdentityDocument
{
    public static function builder(): IdentityDocumentBuilder
    {
        return new IdentityDocumentBuilder();
    }

    private string $guid;

    private string $number;

    private string $issuer;

    private string $issuedOn;

    private ?string $validTo;

    private string $typeId;

    public function __construct(
        string $guid,
        string $number,
        string $issuer,
        string $issuedOn,
        string $typeId,
        ?string $validTo = null,
    ) {
        $this->guid = $guid;
        $this->number = $number;
        $this->issuer = $issuer;
        $this->issuedOn = $issuedOn;
        $this->typeId = $typeId;
        $this->validTo = $validTo;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'guid' => $this->guid,
            'number' => $this->number,
            'issuer' => $this->issuer,
            'issuedOn' => $this->issuedOn,
            'type' => DescriptorPayload::fromId($this->typeId),
        ];

        if ($this->validTo !== null) {
            $payload['validTo'] = $this->validTo;
        }

        return $payload;
    }
}
