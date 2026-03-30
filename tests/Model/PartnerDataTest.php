<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Calculation\PartnerData;
use PHPUnit\Framework\TestCase;

final class PartnerDataTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'returnToShopUrl' => 'https://shop.example.com/return',
            'returnButtonLabel' => 'Wróć do sklepu',
        ];

        $result = PartnerData::fromArray($data);

        self::assertSame('https://shop.example.com/return', $result->returnToShopUrl);
        self::assertSame('Wróć do sklepu', $result->returnButtonLabel);
    }

    public function testFromArrayParsesMinimalPayload(): void
    {
        $data = [];

        $result = PartnerData::fromArray($data);

        self::assertNull($result->returnToShopUrl);
        self::assertNull($result->returnButtonLabel);
    }

    public function testFromArrayIgnoresNonStringValues(): void
    {
        $data = [
            'returnToShopUrl' => 123,
            'returnButtonLabel' => ['nested'],
        ];

        $result = PartnerData::fromArray($data);

        self::assertNull($result->returnToShopUrl);
        self::assertNull($result->returnButtonLabel);
    }
}
