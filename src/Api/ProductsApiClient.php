<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Api;

use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpResponse;

/**
 * Low-level client for /Products endpoints.
 */
final class ProductsApiClient
{
    private EflHttpClient $http;

    public function __construct(EflHttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /Products/GetSectorClassAndType
     */
    public function getSectorClassAndType(string $transactionId, string $bearerToken): HttpResponse
    {
        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Products/GetSectorClassAndType',
            $bearerToken,
            ['transactionId' => $transactionId],
        );
    }

    /**
     * GET /Products/GetBrandModelByProductTypeIdAndPartnerGuid
     */
    public function getBrandModelByProductTypeIdAndPartnerGuid(
        string $transactionId,
        int $productTypeId,
        string $bearerToken,
    ): HttpResponse {
        return $this->http->requestWithBearerToken(
            'GET',
            '/lon/api/v1/Products/GetBrandModelByProductTypeIdAndPartnerGuid',
            $bearerToken,
            [
                'transactionId' => $transactionId,
                'productTypeId' => (string) $productTypeId,
            ],
        );
    }
}
