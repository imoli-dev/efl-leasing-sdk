<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Customer\Phone;

/**
 * Fluent builder for Phone model.
 */
final class PhoneBuilder
{
    private ?string $guid = null;

    private ?string $prefix = null;

    private ?string $number = null;

    private ?string $typeId = null;

    private ?string $kindId = null;

    public function withGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function withPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function withNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function withTypeId(string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function withKindId(string $kindId): self
    {
        $this->kindId = $kindId;

        return $this;
    }

    public function build(): Phone
    {
        if ($this->guid === null || $this->prefix === null || $this->number === null
            || $this->typeId === null || $this->kindId === null) {
            throw new \LogicException('guid, prefix, number, typeId and kindId are required to build Phone');
        }

        return new Phone(
            $this->guid,
            $this->prefix,
            $this->number,
            $this->typeId,
            $this->kindId,
        );
    }

    public static function create(string $guid, string $prefix, string $number, string $typeId, string $kindId): self
    {
        return (new self())
            ->withGuid($guid)
            ->withPrefix($prefix)
            ->withNumber($number)
            ->withTypeId($typeId)
            ->withKindId($kindId);
    }
}
