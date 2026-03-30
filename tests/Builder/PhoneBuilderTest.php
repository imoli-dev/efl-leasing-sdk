<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Builder;

use Imoli\EflLeasingSdk\Builder\PhoneBuilder;
use Imoli\EflLeasingSdk\Model\Customer\Phone;
use PHPUnit\Framework\TestCase;

final class PhoneBuilderTest extends TestCase
{
    public function testBuildReturnsPhone(): void
    {
        $phone = Phone::builder()
            ->withGuid('phone-guid')
            ->withPrefix('+48')
            ->withNumber('123456789')
            ->withTypeId('mobile')
            ->withKindId('mobile')
            ->build();

        self::assertInstanceOf(Phone::class, $phone);
        self::assertSame('123456789', $phone->toRequestPayload()['number']);
    }

    public function testCreateShortcutBuildsCorrectly(): void
    {
        $phone = PhoneBuilder::create('g', '+48', '123', 'mobile', 'mobile')->build();

        self::assertSame('123', $phone->toRequestPayload()['number']);
    }

    public function testBuildThrowsWhenRequiredFieldMissing(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('guid, prefix, number, typeId and kindId are required');

        Phone::builder()->withGuid('g')->build();
    }
}
