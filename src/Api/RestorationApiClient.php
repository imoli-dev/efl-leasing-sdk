<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;

/**
 * Low-level client for /Restoration endpoints.
 */
final class RestorationApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /Restoration/RestoreCustomerSession
     */
    public function restoreCustomerSession(string $payByLinkOrderId): HttpResponse
    {
        return $this->http->request(
            'GET',
            '/lon/api/v1/Restoration/RestoreCustomerSession',
            ['payByLinkOrderId' => $payByLinkOrderId],
        );
    }

    /**
     * GET /Restoration/RestoreSessionAfterSigning
     */
    public function restoreSessionAfterSigning(string $transactionId): HttpResponse
    {
        return $this->http->request(
            'GET',
            '/lon/api/v1/Restoration/RestoreSessionAfterSigning',
            ['transactionId' => $transactionId],
        );
    }
}
