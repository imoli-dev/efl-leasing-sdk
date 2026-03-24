<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Lead\Prospect;

/**
 * Fluent builder for Prospect model.
 */
final class ProspectBuilder
{
    private ?string $firstName = null;

    private ?string $lastName = null;

    private ?string $nip = null;

    private ?string $postal = null;

    private ?string $phoneNo = null;

    private ?string $email = null;

    private ?string $description = null;

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

    public function withPostal(string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function withPhoneNo(string $phoneNo): self
    {
        $this->phoneNo = $phoneNo;

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function build(): Prospect
    {
        if ($this->firstName === null || $this->lastName === null || $this->nip === null
            || $this->postal === null || $this->phoneNo === null || $this->email === null) {
            throw new \LogicException(
                'firstName, lastName, nip, postal, phoneNo and email are required to build Prospect'
            );
        }

        return new Prospect(
            $this->firstName,
            $this->lastName,
            $this->nip,
            $this->postal,
            $this->phoneNo,
            $this->email,
            $this->description,
        );
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $nip,
        string $postal,
        string $phoneNo,
        string $email,
    ): self {
        return (new self())
            ->withFirstName($firstName)
            ->withLastName($lastName)
            ->withNip($nip)
            ->withPostal($postal)
            ->withPhoneNo($phoneNo)
            ->withEmail($email);
    }
}
