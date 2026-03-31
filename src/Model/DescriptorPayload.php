<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model;

/**
 * Builds Descriptor objects required by the EFL API for dictionary-backed fields.
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (Descriptor schema)
 */
final class DescriptorPayload
{
    /**
     * @return array{id: string, name: string, version: array{major: int, minor: int, patch: int}}
     */
    public static function fromId(string $id): array
    {
        return [
            'id' => $id,
            'name' => $id,
            'version' => ['major' => 1, 'minor' => 0, 'patch' => 0],
        ];
    }
}
