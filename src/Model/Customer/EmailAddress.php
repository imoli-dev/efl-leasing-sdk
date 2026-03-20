<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\EmailAddressBuilder;

final class EmailAddress
{
    public static function builder(): EmailAddressBuilder
    {
        return new EmailAddressBuilder();
    }

    private string $guid;

    private string $email;

    private string $typeId;

    public function __construct(string $guid, string $email, string $typeId)
    {
        $this->guid = $guid;
        $this->email = $email;
        $this->typeId = $typeId;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        return [
            'guid' => $this->guid,
            'email' => $this->email,
            'type' => [
                'id' => $this->typeId,
            ],
        ];
    }
}
