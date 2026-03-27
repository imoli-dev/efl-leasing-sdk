<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests;

use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testItStoresProvidedValues(): void
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test/api/',
        );

        self::assertSame('test-key', $config->getApiKey());
        self::assertSame(Environment::Sandbox, $config->getEnvironment());
        self::assertSame('https://example.test/api', $config->getBaseUrl());
    }

    public function testSandboxFactoryReturnsCorrectConfig(): void
    {
        $config = Config::sandbox('api-key');

        self::assertSame('api-key', $config->getApiKey());
        self::assertSame(Environment::Sandbox, $config->getEnvironment());
        self::assertSame('https://leasingonlineapi-sandbox.efl.com.pl', $config->getBaseUrl());
    }

    public function testProductionFactoryReturnsCorrectConfig(): void
    {
        $config = Config::production('api-key', 'https://api.example.com');

        self::assertSame('api-key', $config->getApiKey());
        self::assertSame(Environment::Production, $config->getEnvironment());
        self::assertSame('https://api.example.com', $config->getBaseUrl());
    }

}
