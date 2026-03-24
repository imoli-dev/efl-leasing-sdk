<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;

/**
 * Fluent builder for CustomerDataStatement model.
 */
final class CustomerDataStatementBuilder
{
    private ?string $guid = null;

    private ?bool $agreement = null;

    private ?string $statementTypeId = null;

    private ?string $validFrom = null;

    public function withGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function withAgreement(bool $agreement): self
    {
        $this->agreement = $agreement;

        return $this;
    }

    public function withStatementTypeId(string $statementTypeId): self
    {
        $this->statementTypeId = $statementTypeId;

        return $this;
    }

    public function withValidFrom(?string $validFrom): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function build(): CustomerDataStatement
    {
        if ($this->guid === null || $this->agreement === null || $this->statementTypeId === null) {
            throw new \LogicException('guid, agreement and statementTypeId are required to build CustomerDataStatement');
        }

        return new CustomerDataStatement(
            $this->guid,
            $this->agreement,
            $this->statementTypeId,
            $this->validFrom,
        );
    }

    public static function create(string $guid, bool $agreement, string $statementTypeId): self
    {
        return (new self())
            ->withGuid($guid)
            ->withAgreement($agreement)
            ->withStatementTypeId($statementTypeId);
    }
}
