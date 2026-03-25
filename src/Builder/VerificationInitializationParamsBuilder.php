<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;

/**
 * Fluent builder for VerificationInitializationParams model.
 */
final class VerificationInitializationParamsBuilder
{
    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $residenceAddressStreet = null;

    private ?string $residenceAddressHouseNumber = null;

    private ?string $residenceAddressPostalCode = null;

    private ?string $residenceAddressCity = null;

    private ?string $email = null;

    public function withFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function withLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function withResidenceAddressStreet(string $street): self
    {
        $this->residenceAddressStreet = $street;

        return $this;
    }

    public function withResidenceAddressHouseNumber(string $houseNumber): self
    {
        $this->residenceAddressHouseNumber = $houseNumber;

        return $this;
    }

    public function withResidenceAddressPostalCode(string $postalCode): self
    {
        $this->residenceAddressPostalCode = $postalCode;

        return $this;
    }

    public function withResidenceAddressCity(string $city): self
    {
        $this->residenceAddressCity = $city;

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function build(): VerificationInitializationParams
    {
        if ($this->firstName === null || $this->lastName === null || $this->residenceAddressStreet === null
            || $this->residenceAddressHouseNumber === null || $this->residenceAddressPostalCode === null
            || $this->residenceAddressCity === null || $this->email === null) {
            throw new \LogicException(
                'firstName, lastName, residenceAddressStreet, residenceAddressHouseNumber, '
                . 'residenceAddressPostalCode, residenceAddressCity and email are required to build VerificationInitializationParams'
            );
        }

        return new VerificationInitializationParams(
            $this->firstName,
            $this->lastName,
            $this->residenceAddressStreet,
            $this->residenceAddressHouseNumber,
            $this->residenceAddressPostalCode,
            $this->residenceAddressCity,
            $this->email,
        );
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $residenceAddressStreet,
        string $residenceAddressHouseNumber,
        string $residenceAddressPostalCode,
        string $residenceAddressCity,
        string $email,
    ): self {
        return (new self())
            ->withFirstName($firstName)
            ->withLastName($lastName)
            ->withResidenceAddressStreet($residenceAddressStreet)
            ->withResidenceAddressHouseNumber($residenceAddressHouseNumber)
            ->withResidenceAddressPostalCode($residenceAddressPostalCode)
            ->withResidenceAddressCity($residenceAddressCity)
            ->withEmail($email);
    }
}
