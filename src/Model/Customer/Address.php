<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\AddressBuilder;
use Imoli\EflLeasingSdk\Model\DescriptorPayload;

/**
 * Represents a postal address used in customer and company data.
 */
final class Address
{
    public static function builder(): AddressBuilder
    {
        return new AddressBuilder();
    }

    private string $guid;

    private string $name;

    private string $typeId;

    private string $city;

    private string $street;

    private string $houseNumber;

    private ?string $flatNumber;

    private string $postalCode;

    private string $countryCode;

    public function __construct(
        string $guid,
        string $name,
        string $typeId,
        string $city,
        string $street,
        string $houseNumber,
        string $postalCode,
        string $countryCode,
        ?string $flatNumber = null,
    ) {
        $this->guid = $guid;
        $this->name = $name;
        $this->typeId = $typeId;
        $this->city = $city;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->postalCode = $postalCode;
        $this->countryCode = $countryCode;
        $this->flatNumber = $flatNumber;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'guid' => $this->guid,
            'name' => $this->name,
            'type' => DescriptorPayload::fromId($this->typeId),
            'city' => $this->city,
            'street' => $this->street,
            'houseNumber' => $this->houseNumber,
            'postal' => DescriptorPayload::fromId($this->postalCode),
            'country' => DescriptorPayload::fromId($this->countryCode),
        ];

        if ($this->flatNumber !== null) {
            $payload['flatNumber'] = $this->flatNumber;
        }

        return $payload;
    }
}
