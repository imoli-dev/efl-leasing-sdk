<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\IdentityDocumentBuilder;
use Imoli\EflLeasingSdk\Model\Customer\IdentityDocument;
use PHPUnit\Framework\TestCase;

final class IdentityDocumentBuilderTest extends TestCase
{
    public function testBuildReturnsIdentityDocument(): void
    {
        $doc = IdentityDocument::builder()
            ->withGuid('doc-guid')
            ->withNumber('ABC123')
            ->withIssuer('City Hall')
            ->withIssuedOn('2020-01-15')
            ->withTypeId('id_card')
            ->build();

        self::assertInstanceOf(IdentityDocument::class, $doc);
        $payload = $doc->toRequestPayload();
        self::assertSame('ABC123', $payload['number']);
        self::assertSame('id_card', $payload['type']['id']);
    }

    public function testBuildIncludesValidToWhenSet(): void
    {
        $doc = IdentityDocument::builder()
            ->withGuid('doc-guid')
            ->withNumber('ABC123')
            ->withIssuer('City Hall')
            ->withIssuedOn('2020-01-15')
            ->withTypeId('id_card')
            ->withValidTo('2030-01-15')
            ->build();

        self::assertSame('2030-01-15', $doc->toRequestPayload()['validTo']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $doc = IdentityDocumentBuilder::create('g', '123', 'Issuer', '2020-01-01', 'passport')->build();

        self::assertSame('123', $doc->toRequestPayload()['number']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('guid, number, issuer, issuedOn and typeId are required');

        IdentityDocument::builder()
            ->withGuid('g')
            ->withNumber('123')
            ->build();
    }
}
