<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;
use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;

/**
 * Low-level client for /Process and /Restoration endpoints.
 */
final class ProcessApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /Process/Init
     */
    public function init(string $bearerToken, ?string $positiveUrlResponse = null, ?string $negativeUrlResponse = null): HttpResponse
    {
        $query = [];

        if ($positiveUrlResponse !== null) {
            $query['PositiveUrlResponse'] = $positiveUrlResponse;
        }

        if ($negativeUrlResponse !== null) {
            $query['NegativeUrlResponse'] = $negativeUrlResponse;
        }

        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Process/Init',
            $bearerToken,
            $query,
        );
    }

    /**
     * GET /Process/GetToken
     */
    public function getToken(string $partnerId): HttpResponse
    {
        return $this->http->requestWithApiKey(
            'GET',
            '/lon/api/v1/Process/GetToken',
            ['partnerId' => $partnerId],
        );
    }

    /**
     * GET /Process/GetChanges
     *
     * @param string[]|null $statusBpm
     */
    public function getChanges(string $transactionId, ?array $statusBpm, string $bearerToken): HttpResponse
    {
        $query = ['transactionId' => $transactionId];

        if ($statusBpm !== null) {
            $query['statusBPM'] = $statusBpm;
        }

        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Process/GetChanges',
            $bearerToken,
            $query,
        );
    }

    /**
     * GET /Process/GetLastStatus
     */
    public function getLastStatus(string $transactionId, string $bearerToken): HttpResponse
    {
        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Process/GetLastStatus',
            $bearerToken,
            ['transactionId' => $transactionId],
        );
    }

    /**
     * POST /Process/PostVerificationCode
     */
    public function postVerificationCode(PostVerificationCode $code, string $bearerToken): HttpResponse
    {
        $body = json_encode($code->toRequestPayload(), JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Process/PostVerificationCode',
            $bearerToken,
            [],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

    /**
     * GET /Process/GetPostVerificationCodeChanges
     */
    public function getPostVerificationCodeChanges(string $transactionId, string $bearerToken): HttpResponse
    {
        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Process/GetPostVerificationCodeChanges',
            $bearerToken,
            ['transactionId' => $transactionId],
        );
    }

    /**
     * GET /Process/GetRestoreProcess
     */
    public function getRestoreProcess(string $transactionId): HttpResponse
    {
        return $this->http->request(
            'GET',
            '/lon/api/v1/Process/GetRestoreProcess',
            ['transactionId' => $transactionId],
        );
    }

    /**
     * GET /Process/GetRestoreProcessChanges
     */
    public function getRestoreProcessChanges(string $transactionId): HttpResponse
    {
        return $this->http->request(
            'GET',
            '/lon/api/v1/Process/GetRestoreProcessChanges',
            ['transactionId' => $transactionId],
        );
    }

    /**
     * POST /Process/SetProcessTypeForCompany
     */
    public function setProcessTypeForCompany(
        string $transactionId,
        string $bearerToken,
        ?string $nip = null,
        ?bool $basketCalculation = null,
    ): HttpResponse {
        $query = ['transactionId' => $transactionId];

        if ($nip !== null) {
            $query['nip'] = $nip;
        }

        if ($basketCalculation !== null) {
            $query['basketCalculation'] = $basketCalculation ? 'true' : 'false';
        }

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Process/SetProcessTypeForCompany',
            $bearerToken,
            $query,
        );
    }
}
