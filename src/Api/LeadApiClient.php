<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;
use Imoli\EflLeasingSdk\Model\Lead\ContactData;

/**
 * Low-level client for /Lead endpoints.
 */
final class LeadApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * POST /Lead/SendContactForm
     */
    public function sendContactForm(
        string $transactionId,
        ContactData $contactData,
        string $bearerToken,
    ): HttpResponse {
        $body = json_encode($contactData->toRequestPayload(), JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Lead/SendContactForm',
            $bearerToken,
            ['transactionId' => $transactionId],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }
}
