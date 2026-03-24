<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;

/**
 * Fluent builder for IdentityDocument model.
 */
final class IdentityDocumentBuilder
{
    private ?string $guid = null;

    private ?string $number = null;

    private ?string $issuer = null;

    private ?string $issuedOn = null;

    private ?string $typeId = null;

    private ?string $validTo = null;

    public function withGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function withNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function withIssuer(string $issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function withIssuedOn(string $issuedOn): self
    {
        $this->issuedOn = $issuedOn;

        return $this;
    }

    public function withTypeId(string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function withValidTo(?string $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function build(): IdentityDocument
    {
        if ($this->guid === null || $this->number === null || $this->issuer === null
            || $this->issuedOn === null || $this->typeId === null) {
            throw new \LogicException('guid, number, issuer, issuedOn and typeId are required to build IdentityDocument');
        }

        return new IdentityDocument(
            $this->guid,
            $this->number,
            $this->issuer,
            $this->issuedOn,
            $this->typeId,
            $this->validTo,
        );
    }

    public static function create(string $guid, string $number, string $issuer, string $issuedOn, string $typeId): self
    {
        return (new self())
            ->withGuid($guid)
            ->withNumber($number)
            ->withIssuer($issuer)
            ->withIssuedOn($issuedOn)
            ->withTypeId($typeId);
    }
}
