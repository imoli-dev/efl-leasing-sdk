<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Restoration\ReauthenticationAction;
use PHPUnit\Framework\TestCase;

final class ReauthenticationActionTest extends TestCase
{
    public function testEnumValues(): void
    {
        self::assertSame('None', ReauthenticationAction::None->value);
        self::assertSame('PaymentPending', ReauthenticationAction::PaymentPending->value);
        self::assertSame('PaymentAccepted', ReauthenticationAction::PaymentAccepted->value);
    }

    public function testFromString(): void
    {
        self::assertSame(ReauthenticationAction::None, ReauthenticationAction::from('None'));
        self::assertSame(ReauthenticationAction::PaymentPending, ReauthenticationAction::from('PaymentPending'));
        self::assertSame(ReauthenticationAction::PaymentAccepted, ReauthenticationAction::from('PaymentAccepted'));
    }
}
