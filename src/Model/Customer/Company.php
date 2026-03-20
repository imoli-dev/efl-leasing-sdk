<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\CompanyBuilder;

/**
 * Represents company data required by EFL.
 */
final class Company
{
    public static function builder(string $guid, string $nip): CompanyBuilder
    {
        return new CompanyBuilder($guid, $nip);
    }

    private string $guid;

    private string $nip;

    /**
     * @var EmailAddress[]
     */
    private array $emails;

    /**
     * @var Phone[]
     */
    private array $phones;

    /**
     * @var Person[]
     */
    private array $persons;

    /**
     * @var Address[]
     */
    private array $addresses;

    /**
     * @var CustomerDataStatement[]
     */
    private array $statements;

    /**
     * @param EmailAddress[] $emails
     * @param Phone[] $phones
     * @param Person[] $persons
     * @param Address[] $addresses
     * @param CustomerDataStatement[] $statements
     */
    public function __construct(
        string $guid,
        string $nip,
        array $emails,
        array $phones,
        array $persons,
        array $addresses,
        array $statements,
    ) {
        $this->guid = $guid;
        $this->nip = $nip;
        $this->emails = $emails;
        $this->phones = $phones;
        $this->persons = $persons;
        $this->addresses = $addresses;
        $this->statements = $statements;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $emails = [];
        foreach ($this->emails as $email) {
            $emails[] = $email->toRequestPayload();
        }

        $phones = [];
        foreach ($this->phones as $phone) {
            $phones[] = $phone->toRequestPayload();
        }

        $persons = [];
        foreach ($this->persons as $person) {
            $persons[] = $person->toRequestPayload();
        }

        $addresses = [];
        foreach ($this->addresses as $address) {
            $addresses[] = $address->toRequestPayload();
        }

        $statements = [];
        foreach ($this->statements as $statement) {
            $statements[] = $statement->toRequestPayload();
        }

        return [
            'guid' => $this->guid,
            'nip' => $this->nip,
            'emails' => $emails,
            'phones' => $phones,
            'persons' => $persons,
            'addresses' => $addresses,
            'statements' => $statements,
        ];
    }
}
