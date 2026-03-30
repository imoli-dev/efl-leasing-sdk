<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\ContactDataBuilder;
use Imoli\EflLeasingSdk\Model\Lead\ContactData;
use Imoli\EflLeasingSdk\Model\Lead\Prospect;
use Imoli\EflLeasingSdk\Model\Lead\StatementLead;
use PHPUnit\Framework\TestCase;

final class ContactDataBuilderTest extends TestCase
{
    public function testBuildReturnsContactData(): void
    {
        $prospect = new Prospect(
            'Jan',
            'Kowalski',
            '1234567890',
            '00-001',
            '+48123456789',
            'jan@example.com',
        );

        $contactData = (new ContactDataBuilder())
            ->withProspect($prospect)
            ->addStatementLead(new StatementLead(1))
            ->build();

        self::assertInstanceOf(ContactData::class, $contactData);

        $payload = $contactData->toRequestPayload();
        self::assertSame('Jan', $payload['prospect']['firstName']);
        self::assertCount(1, $payload['statementLead']);
    }

    public function testBuildThrowsWhenProspectNotSet(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Prospect is required to build ContactData');

        (new ContactDataBuilder())->build();
    }

    public function testBuildReturnsContactDataWithEmptyStatements(): void
    {
        $prospect = new Prospect(
            'Jan',
            'Kowalski',
            '1234567890',
            '00-001',
            '+48123456789',
            'jan@example.com',
        );

        $contactData = (new ContactDataBuilder())
            ->withProspect($prospect)
            ->build();

        $payload = $contactData->toRequestPayload();
        self::assertSame([], $payload['statementLead']);
    }

    public function testContactDataBuilderReturnsBuilderFromModel(): void
    {
        $builder = ContactData::builder();

        self::assertInstanceOf(ContactDataBuilder::class, $builder);
    }
}
