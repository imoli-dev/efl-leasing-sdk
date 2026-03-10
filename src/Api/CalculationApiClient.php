<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;

/**
 * Low-level client for /Calculation endpoints.
 */
final class CalculationApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * POST /Calculation/CalculateBasicOffer
     */
    public function calculateBasicOffer(AssetToCalculation $basket, string $bearerToken): HttpResponse
    {
        $payload = $basket->toRequestPayload();
        $transactionId = $payload['transactionId'];

        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Calculation/CalculateBasicOffer',
            $bearerToken,
            ['transactionId' => $transactionId],
            ['Content-Type' => 'application/json'],
            $body,
        );
    }

    /**
     * GET /Calculation/GetBaseData
     */
    public function getBaseData(string $transactionId, string $bearerToken): HttpResponse
    {
        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Calculation/GetBaseData',
            $bearerToken,
            ['transactionId' => $transactionId],
        );
    }

    /**
     * POST /Calculation/AcceptCalculation
     */
    public function acceptCalculation(
        string $transactionId,
        int $calculationId,
        int $calculationVariantId,
        ?bool $basketCalculation,
        string $bearerToken,
    ): HttpResponse {
        $query = ['transactionId' => $transactionId];

        if ($basketCalculation !== null) {
            $query['basketCalculation'] = $basketCalculation ? 'true' : 'false';
        }

        $body = json_encode([
            'calculationId' => $calculationId,
            'calculationVariantId' => $calculationVariantId,
        ], JSON_THROW_ON_ERROR);

        return $this->http->requestWithBearerToken(
            'POST',
            '/lon/api/v1/Calculation/AcceptCalculation',
            $bearerToken,
            $query,
            ['Content-Type' => 'application/json'],
            $body,
        );
    }
}
