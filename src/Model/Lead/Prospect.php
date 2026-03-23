<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Lead;

use Imoli\EflLeasingSdk\Builder\ProspectBuilder;

final class Prospect
{
    public static function builder(): ProspectBuilder
    {
        return new ProspectBuilder();
    }

    private string $firstName;

    private string $lastName;

    private string $nip;

    private string $postal;

    private string $phoneNo;

    private string $email;

    private ?string $description;

    public function __construct(
        string $firstName,
        string $lastName,
        string $nip,
        string $postal,
        string $phoneNo,
        string $email,
        ?string $description = null,
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nip = $nip;
        $this->postal = $postal;
        $this->phoneNo = $phoneNo;
        $this->email = $email;
        $this->description = $description;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        $payload = [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'nip' => $this->nip,
            'postal' => $this->postal,
            'phoneNo' => $this->phoneNo,
            'email' => $this->email,
        ];

        if ($this->description !== null) {
            $payload['description'] = $this->description;
        }

        return $payload;
    }
}
