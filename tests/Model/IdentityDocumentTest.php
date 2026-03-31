<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;
use PHPUnit\Framework\TestCase;

final class IdentityDocumentTest extends TestCase
{
    public function testToRequestPayloadReturnsMinimalStructure(): void
    {
        $doc = new IdentityDocument(
            'doc-guid',
            'ABC123456',
            'City Hall Warsaw',
            '2020-01-15',
            'id_card',
        );

        $payload = $doc->toRequestPayload();

        self::assertSame('doc-guid', $payload['guid']);
        self::assertSame('ABC123456', $payload['number']);
        self::assertSame('City Hall Warsaw', $payload['issuer']);
        self::assertSame('2020-01-15', $payload['issuedOn']);
        self::assertSame('id_card', $payload['type']['id']);
        self::assertSame('id_card', $payload['type']['name']);
        self::assertSame(['major' => 1, 'minor' => 0, 'patch' => 0], $payload['type']['version']);
        self::assertArrayNotHasKey('validTo', $payload);
    }

    public function testToRequestPayloadIncludesValidToWhenSet(): void
    {
        $doc = new IdentityDocument(
            'doc-guid',
            'PAS123456',
            'Passport Office',
            '2019-06-01',
            'passport',
            '2029-06-01',
        );

        $payload = $doc->toRequestPayload();

        self::assertSame('2029-06-01', $payload['validTo']);
    }
}
