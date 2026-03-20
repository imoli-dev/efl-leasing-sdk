<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Customer;

use Imoli\EflLeasingSdk\Builder\PhoneBuilder;

final class Phone
{
    public static function builder(): PhoneBuilder
    {
        return new PhoneBuilder();
    }

    private string $guid;

    private string $prefix;

    private string $number;

    private string $typeId;

    private string $kindId;

    public function __construct(
        string $guid,
        string $prefix,
        string $number,
        string $typeId,
        string $kindId,
    ) {
        $this->guid = $guid;
        $this->prefix = $prefix;
        $this->number = $number;
        $this->typeId = $typeId;
        $this->kindId = $kindId;
    }

    /**
     * @return array<string, mixed>
     */
    public function toRequestPayload(): array
    {
        return [
            'guid' => $this->guid,
            'prefix' => $this->prefix,
            'number' => $this->number,
            'type' => [
                'id' => $this->typeId,
            ],
            'kind' => [
                'id' => $this->kindId,
            ],
        ];
    }
}
