<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\Address;
use Imoli\EflLeasingSdk\Model\Customer\Company;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;
use Imoli\EflLeasingSdk\Model\Customer\Person;
use Imoli\EflLeasingSdk\Model\Customer\Phone;

/**
 * Fluent builder for Company model.
 */
final class CompanyBuilder
{
    private string $guid;

    private string $nip;

    /** @var EmailAddress[] */
    private array $emails = [];

    /** @var Phone[] */
    private array $phones = [];

    /** @var Person[] */
    private array $persons = [];

    /** @var Address[] */
    private array $addresses = [];

    /** @var CustomerDataStatement[] */
    private array $statements = [];

    public function __construct(string $guid, string $nip)
    {
        $this->guid = $guid;
        $this->nip = $nip;
    }

    public function addEmail(EmailAddress $email): self
    {
        $this->emails[] = $email;

        return $this;
    }

    public function addPhone(Phone $phone): self
    {
        $this->phones[] = $phone;

        return $this;
    }

    public function addPerson(Person $person): self
    {
        $this->persons[] = $person;

        return $this;
    }

    public function addAddress(Address $address): self
    {
        $this->addresses[] = $address;

        return $this;
    }

    public function addStatement(CustomerDataStatement $statement): self
    {
        $this->statements[] = $statement;

        return $this;
    }

    public function build(): Company
    {
        return new Company(
            $this->guid,
            $this->nip,
            $this->emails,
            $this->phones,
            $this->persons,
            $this->addresses,
            $this->statements,
        );
    }
}
