<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\VerificationInitializationParamsBuilder;
use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;
use PHPUnit\Framework\TestCase;

final class VerificationInitializationParamsBuilderTest extends TestCase
{
    public function testBuildReturnsVerificationInitializationParams(): void
    {
        $params = VerificationInitializationParams::builder()
            ->withFirstName('Jan')
            ->withLastName('Kowalski')
            ->withResidenceAddressStreet('Main St')
            ->withResidenceAddressHouseNumber('1')
            ->withResidenceAddressPostalCode('00-001')
            ->withResidenceAddressCity('Warsaw')
            ->withEmail('jan@example.com')
            ->build();

        self::assertInstanceOf(VerificationInitializationParams::class, $params);
        $payload = $params->toRequestPayload();
        self::assertSame('Jan', $payload['firstName']);
        self::assertSame('Main St', $payload['residenceAddressStreet']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $params = VerificationInitializationParamsBuilder::create(
            'Jan',
            'Kowalski',
            'St',
            '1',
            '00-001',
            'Warsaw',
            'j@x.com',
        )->build();

        self::assertSame('Warsaw', $params->toRequestPayload()['residenceAddressCity']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('firstName, lastName, residenceAddressStreet');

        VerificationInitializationParams::builder()->withFirstName('Jan')->build();
    }
}
