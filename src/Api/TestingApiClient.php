<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;

/**
 * Low-level client for /Testing endpoints (sandbox utilities).
 */
final class TestingApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * POST /Testing/SendITN
     *
     * @param array<string, mixed> $transactionListFlattened
     */
    public function sendItn(array $transactionListFlattened, string $bearerToken): HttpResponse
    {
        $body = json_encode($transactionListFlattened, JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Testing/SendITN',
            $bearerToken,
            [],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

}
