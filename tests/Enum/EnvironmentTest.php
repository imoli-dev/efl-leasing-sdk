<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Enum;

use Imoli\EflLeasingSdk\Enum\Environment;
use PHPUnit\Framework\TestCase;

final class EnvironmentTest extends TestCase
{
    public function testSandboxValue(): void
    {
        self::assertSame('sandbox', Environment::Sandbox->value);
    }

    public function testProductionValue(): void
    {
        self::assertSame('production', Environment::Production->value);
    }

    public function testFromString(): void
    {
        self::assertSame(Environment::Sandbox, Environment::from('sandbox'));
        self::assertSame(Environment::Production, Environment::from('production'));
    }
}
