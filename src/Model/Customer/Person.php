<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\PersonBuilder;
use Imoli\EflLeasingSdk\Model\DescriptorPayload;

final class Person
{
    public static function builder(): PersonBuilder
    {
        return new PersonBuilder();
    }

    private string $guid;

    private string $firstName;

    private ?string $middleName;

    private string $lastName;

    private string $nip;

    private string $pesel;

    private string $birthDate;

    private string $birthPlace;

    private bool $pep;

    private Address $address;

    private string $countryOfOriginId;

    /**
     * @var IdentityDocument[]
     */
    private array $identityDocuments;

    /**
     * @param IdentityDocument[] $identityDocuments
     * @param CustomerDataStatement[]|null $statements
     */
    public function __construct(
        string $guid,
        string $firstName,
        string $lastName,
        string $nip,
        string $pesel,
        string $birthDate,
        string $birthPlace,
        bool $pep,
        Address $address,
        string $countryOfOriginId,
        array $identityDocuments,
        ?string $middleName = null,
        ?array $statements = null,
    ) {
        $this->guid = $guid;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->nip = $nip;
        $this->pesel = $pesel;
        $this->birthDate = $birthDate;
        $this->birthPlace = $birthPlace;
        $this->pep = $pep;
        $this->address = $address;
        $this->countryOfOriginId = $countryOfOriginId;
        $this->identityDocuments = $identityDocuments;
        $this->statements = $statements;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'guid' => $this->guid,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'nip' => $this->nip,
            'pesel' => $this->pesel,
            'birthDate' => $this->birthDate,
            'birthPlace' => $this->birthPlace,
            'pep' => $this->pep,
            'address' => $this->address->toRequestPayload(),
            'countryOfOrigin' => DescriptorPayload::fromId($this->countryOfOriginId),
            'identityDocuments' => array_map(
                static fn (IdentityDocument $doc): array => $doc->toRequestPayload(),
                $this->identityDocuments,
            ),
        ];

        if ($this->middleName !== null) {
            $payload['middleName'] = $this->middleName;
        }

        if ($this->statements !== null) {
            $statementsPayload = [];
            foreach ($this->statements as $statement) {
                $statementsPayload[] = $statement->toRequestPayload();
            }

            $payload['statements'] = $statementsPayload;
        }

        return $payload;
    }

    /**
     * @var CustomerDataStatement[]|null
     */
    private ?array $statements;
}
