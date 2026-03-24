<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Lead\ContactData;
use Imoli\EflLeasingSdk\Model\Lead\Prospect;
use Imoli\EflLeasingSdk\Model\Lead\StatementLead;

/**
 * Fluent builder for ContactData model.
 */
final class ContactDataBuilder
{
    private ?Prospect $prospect = null;

    /** @var StatementLead[] */
    private array $statementLeads = [];

    public function withProspect(Prospect $prospect): self
    {
        $this->prospect = $prospect;

        return $this;
    }

    public function addStatementLead(StatementLead $statementLead): self
    {
        $this->statementLeads[] = $statementLead;

        return $this;
    }

    public function build(): ContactData
    {
        if ($this->prospect === null) {
            throw new \LogicException('Prospect is required to build ContactData');
        }

        return new ContactData($this->prospect, $this->statementLeads);
    }
}
