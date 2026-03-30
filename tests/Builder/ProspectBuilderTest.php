<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\ProspectBuilder;
use Imoli\EflLeasingSdk\Model\Lead\Prospect;
use PHPUnit\Framework\TestCase;

final class ProspectBuilderTest extends TestCase
{
    public function testBuildReturnsProspect(): void
    {
        $prospect = Prospect::builder()
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withNip('1234567890')
            ->withPostal('00-001')
            ->withPhoneNo('+48123456789')
            ->withEmail('jan@example.com')
            ->build();

        self::assertInstanceOf(Prospect::class, $prospect);
        self::assertSame('Jan', $prospect->toRequestPayload()['firstName']);
    }

    public function testBuildIncludesDescriptionWhenSet(): void
    {
        $prospect = Prospect::builder()
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withNip('1234567890')
            ->withPostal('00-001')
            ->withPhoneNo('+48123456789')
            ->withEmail('jan@example.com')
            ->withDescription('Interested in leasing')
            ->build();

        self::assertSame('Interested in leasing', $prospect->toRequestPayload()['description']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $prospect = ProspectBuilder::create('Jan', 'Kowalski', '123', '00-001', '+48123', 'j@x.com')->build();

        self::assertSame('Kowalski', $prospect->toRequestPayload()['lastName']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('firstName, lastName, nip, postal, phoneNo and email are required');

        Prospect::builder()->withFirstName('Jan')->build();
    }
}
