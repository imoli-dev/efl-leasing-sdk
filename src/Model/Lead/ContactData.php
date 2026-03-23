<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Lead;

use Imoli\EflLeasingSdk\Builder\ContactDataBuilder;

/**
 * Represents payload for /Lead/SendContactForm.
 */
final class ContactData
{
    public static function builder(): ContactDataBuilder
    {
        return new ContactDataBuilder();
    }

    private Prospect $prospect;

    /**
     * @var StatementLead[]
     */
    private array $statementLeads;

    /**
     * @param StatementLead[] $statementLeads
     */
    public function __construct(Prospect $prospect, array $statementLeads)
    {
        $this->prospect = $prospect;
        $this->statementLeads = $statementLeads;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $statements = [];
        foreach ($this->statementLeads as $statement) {
            $statements[] = $statement->toRequestPayload();
        }

        return [
            'prospect' => $this->prospect->toRequestPayload(),
            'statementLead' => $statements,
        ];
    }
}
