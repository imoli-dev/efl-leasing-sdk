<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;

/**
 * Low-level client for /Demo endpoints.
 */
final class DemoApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /Demo/GetIdentityReturnUrl
     */
    public function getIdentityReturnUrl(): HttpResponse
    {
        return $this->http->request(
            'GET',
            '/lon/api/v1/Demo/GetIdentityReturnUrl',
        );
    }
}
