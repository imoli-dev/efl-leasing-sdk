<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\EmailAddress;

/**
 * Fluent builder for EmailAddress model.
 */
final class EmailAddressBuilder
{
    private ?string $guid = null;

    private ?string $email = null;

    private ?string $typeId = null;

    public function withGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withTypeId(string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function build(): EmailAddress
    {
        if ($this->guid === null || $this->email === null || $this->typeId === null) {
            throw new \LogicException('guid, email and typeId are required to build EmailAddress');
        }

        return new EmailAddress($this->guid, $this->email, $this->typeId);
    }

    public static function create(string $guid, string $email, string $typeId): self
    {
        return (new self())->withGuid($guid)->withEmail($email)->withTypeId($typeId);
    }
}
