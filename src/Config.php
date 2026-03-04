<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk;

use Imoli\EflLeasingSdk\Enum\Environment;

/**
 * Immutable SDK configuration.
 *
 * This value object encapsulates the core settings required to work with
 * the EFL Leasing Online API.
 */
final class Config
{
    private string $apiKey;

    private Environment $environment;

    private string $baseUrl;

    public function __construct(string $apiKey, Environment $environment, string $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->environment = $environment;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public static function sandbox(string $apiKey): self
    {
        return new self(
            $apiKey,
            Environment::Sandbox,
            'https://leasingonlineapi-sandbox.efl.com.pl',
        );
    }

    /**
     * Creates production configuration.
     *
     * @param string $apiKey API key for production.
     * @param string $baseUrl Production API base URL (obtain from EFL when production access is granted).
     */
    public static function production(string $apiKey, string $baseUrl): self
    {
        return new self(
            $apiKey,
            Environment::Production,
            $baseUrl,
        );
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
