<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\Address;

/**
 * Fluent builder for Address model.
 */
final class AddressBuilder
{
    private ?string $guid = null;

    private ?string $name = null;

    private ?string $typeId = null;

    private ?string $city = null;

    private ?string $street = null;

    private ?string $houseNumber = null;

    private ?string $postalCode = null;

    private ?string $countryCode = null;

    private ?string $flatNumber = null;

    public function withGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withTypeId(string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function withCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function withStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function withHouseNumber(string $houseNumber): self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function withPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function withCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function withFlatNumber(?string $flatNumber): self
    {
        $this->flatNumber = $flatNumber;

        return $this;
    }

    public function build(): Address
    {
        if ($this->guid === null || $this->name === null || $this->typeId === null
            || $this->city === null || $this->street === null || $this->houseNumber === null
            || $this->postalCode === null || $this->countryCode === null) {
            throw new \LogicException(
                'guid, name, typeId, city, street, houseNumber, postalCode and countryCode are required to build Address'
            );
        }

        return new Address(
            $this->guid,
            $this->name,
            $this->typeId,
            $this->city,
            $this->street,
            $this->houseNumber,
            $this->postalCode,
            $this->countryCode,
            $this->flatNumber,
        );
    }

    public static function create(
        string $guid,
        string $name,
        string $typeId,
        string $city,
        string $street,
        string $houseNumber,
        string $postalCode,
        string $countryCode,
    ): self {
        return (new self())
            ->withGuid($guid)
            ->withName($name)
            ->withTypeId($typeId)
            ->withCity($city)
            ->withStreet($street)
            ->withHouseNumber($houseNumber)
            ->withPostalCode($postalCode)
            ->withCountryCode($countryCode);
    }
}
