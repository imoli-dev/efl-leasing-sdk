<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Lead\StatementLead;

/**
 * Fluent builder for StatementLead model.
 */
final class StatementLeadBuilder
{
    private ?int $statementConfigurationId = null;

    private ?int $statementCategoryId = null;

    private ?bool $selected = null;

    public function withStatementConfigurationId(int $statementConfigurationId): self
    {
        $this->statementConfigurationId = $statementConfigurationId;

        return $this;
    }

    public function withStatementCategoryId(?int $statementCategoryId): self
    {
        $this->statementCategoryId = $statementCategoryId;

        return $this;
    }

    public function withSelected(?bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }

    public function build(): StatementLead
    {
        if ($this->statementConfigurationId === null) {
            throw new \LogicException('statementConfigurationId is required to build StatementLead');
        }

        return new StatementLead(
            $this->statementConfigurationId,
            $this->statementCategoryId,
            $this->selected,
        );
    }

    public static function create(int $statementConfigurationId): self
    {
        return (new self())->withStatementConfigurationId($statementConfigurationId);
    }
}
