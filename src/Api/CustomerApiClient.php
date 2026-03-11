<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;
use Imoli\EflLeasingSdk\Model\Customer\CustomerData;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;
use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;

/**
 * Low-level client for /Customer endpoints.
 */
final class CustomerApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * POST /Customer/PostCustomerDataForLon
     */
    public function postCustomerDataForLon(CustomerData $data, string $bearerToken): HttpResponse
    {
        $payload = $data->toRequestPayload();

        $query = [
            'transactionId' => $payload['transactionId'],
        ];

        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Customer/PostCustomerDataForLon',
            $bearerToken,
            $query,
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

    /**
     * POST /Customer/PostCustomerStatements
     *
     * @param CustomerDataStatement[] $statements
     */
    public function postCustomerStatements(string $transactionId, array $statements, string $bearerToken): HttpResponse
    {
        $payload = [];

        foreach ($statements as $statement) {
            $payload[] = $statement->toRequestPayload();
        }

        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Customer/PostCustomerStatements',
            $bearerToken,
            ['transactionId' => $transactionId],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

    /**
     * POST /Customer/InitializeIdentityVerification
     */
    public function initializeIdentityVerification(
        string $transactionId,
        VerificationInitializationParams $params,
        string $bearerToken,
    ): HttpResponse {
        $body = json_encode($params->toRequestPayload(), JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Customer/InitializeIdentityVerification',
            $bearerToken,
            ['transactionId' => $transactionId],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

    /**
     * GET /Customer/GetIdentityVerificationStatus
     */
    public function getIdentityVerificationStatus(string $transactionId, string $bearerToken): HttpResponse
    {
        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Customer/GetIdentityVerificationStatus',
            $bearerToken,
            ['transactionId' => $transactionId],
        );
    }

    /**
     * POST /Customer/LeadVerificationResult
     */
    public function leadVerificationResult(
        string $transactionId,
        VerificationResult $result,
        string $bearerToken,
    ): HttpResponse {
        $body = json_encode($result->toRequestPayload(), JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Customer/LeadVerificationResult',
            $bearerToken,
            ['transactionId' => $transactionId],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

    /**
     * POST /Customer/PostInitiatePayByLinkPayment
     */
    public function postInitiatePayByLinkPayment(string $transactionId, string $bearerToken): HttpResponse
    {
        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Customer/PostInitiatePayByLinkPayment',
            $bearerToken,
            ['transactionId' => $transactionId],
        );
    }

    /**
     * POST /Customer/ITN
     *
     * @param string $transactions Raw transactions payload, typically XML.
     */
    public function postItn(string $transactions): HttpResponse
    {
        $boundary = '----efl-sdk-boundary-' . bin2hex(random_bytes(8));

        $body = '';
        $body .= '--' . $boundary . "\r\n";
        $body .= 'Content-Disposition: form-data; name="transactions"' . "\r\n\r\n";
        $body .= $transactions . "\r\n";
        $body .= '--' . $boundary . "--\r\n";

        $headers = [
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
        ];

        return $this->http->request(
            'POST',
            '/lon/api/v1/Customer/ITN',
            [],
            $headers,
            $body,
        );
    }
}
