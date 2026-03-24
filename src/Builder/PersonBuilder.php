<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;
use Imoli\EflLeasingSdk\Model\Customer\Person;

/**
 * Fluent builder for Person model.
 */
final class PersonBuilder
{
    private ?string $guid = null;

    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $nip = null;

    private ?string $pesel = null;

    private ?string $birthDate = null;

    private ?string $birthPlace = null;

    private ?bool $pep = null;

    private ?Address $address = null;

    private ?string $countryOfOriginId = null;

    /** @var IdentityDocument[] */
    private array $identityDocuments = [];

    private ?string $middleName = null;

    /** @var CustomerDataStatement[] */
    private array $statements = [];

    public function withGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

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

    public function withNip(string $nip): self
    {
        $this->nip = $nip;

        return $this;
    }

    public function withPesel(string $pesel): self
    {
        $this->pesel = $pesel;

        return $this;
    }

    public function withBirthDate(string $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function withBirthPlace(string $birthPlace): self
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function withPep(bool $pep): self
    {
        $this->pep = $pep;

        return $this;
    }

    public function withAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function withCountryOfOriginId(string $countryOfOriginId): self
    {
        $this->countryOfOriginId = $countryOfOriginId;

        return $this;
    }

    public function addIdentityDocument(IdentityDocument $document): self
    {
        $this->identityDocuments[] = $document;

        return $this;
    }

    /**
     * @param IdentityDocument[] $documents
     */
    public function withIdentityDocuments(array $documents): self
    {
        $this->identityDocuments = $documents;

        return $this;
    }

    public function withMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function addStatement(CustomerDataStatement $statement): self
    {
        $this->statements[] = $statement;

        return $this;
    }

    /**
     * @param CustomerDataStatement[] $statements
     */
    public function withStatements(array $statements): self
    {
        $this->statements = $statements;

        return $this;
    }

    public function build(): Person
    {
        if ($this->guid === null || $this->firstName === null || $this->lastName === null
            || $this->nip === null || $this->pesel === null || $this->birthDate === null
            || $this->birthPlace === null || $this->pep === null || $this->address === null
            || $this->countryOfOriginId === null) {
            throw new \LogicException(
                'guid, firstName, lastName, nip, pesel, birthDate, birthPlace, pep, address and countryOfOriginId '
                . 'are required to build Person'
            );
        }

        if ($this->identityDocuments === []) {
            throw new \LogicException('At least one identityDocument is required to build Person');
        }

        return new Person(
            $this->guid,
            $this->firstName,
            $this->lastName,
            $this->nip,
            $this->pesel,
            $this->birthDate,
            $this->birthPlace,
            $this->pep,
            $this->address,
            $this->countryOfOriginId,
            $this->identityDocuments,
            $this->middleName,
            $this->statements === [] ? null : $this->statements,
        );
    }
}
