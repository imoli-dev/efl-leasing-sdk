<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Lead;

use Imoli\EflLeasingSdk\Builder\StatementLeadBuilder;

final class StatementLead
{
    public static function builder(): StatementLeadBuilder
    {
        return new StatementLeadBuilder();
    }

    private int $statementConfigurationId;

    private ?int $statementCategoryId;

    private ?bool $selected;

    public function __construct(
        int $statementConfigurationId,
        ?int $statementCategoryId = null,
        ?bool $selected = null,
    ) {
        $this->statementConfigurationId = $statementConfigurationId;
        $this->statementCategoryId = $statementCategoryId;
        $this->selected = $selected;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'statementConfigurationId' => $this->statementConfigurationId,
        ];

        if ($this->statementCategoryId !== null) {
            $payload['statementCategoryId'] = $this->statementCategoryId;
        }

        if ($this->selected !== null) {
            $payload['selected'] = $this->selected;
        }

        return $payload;
    }
}
