<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Integration;

use GuzzleHttp\Client;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\EflClient;
use Imoli\EflLeasingSdk\Exception\ApiException;
use Imoli\EflLeasingSdk\Http\Adapter\GuzzleHttpAdapter;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests against the EFL Leasing Online sandbox API.
 *
 * These tests require valid sandbox credentials. Set the following environment
 * variables to run them:
 *
 *   EFL_API_KEY     - API key for the sandbox
 *   EFL_PARTNER_ID  - Partner identifier for token requests
 *
 * Without these variables, all tests are skipped.
 *
 * @group integration
 */
final class EflClientIntegrationTest extends TestCase
{
    private static ?string $apiKey = null;

    private static ?string $partnerId = null;

    public static function setUpBeforeClass(): void
    {
        $apiKey = getenv('EFL_API_KEY') ?: '';
        $partnerId = getenv('EFL_PARTNER_ID') ?: '';

        if ($apiKey === '' || $partnerId === '') {
            return;
        }

        self::$apiKey = $apiKey;
        self::$partnerId = $partnerId;
    }

    protected function setUp(): void
    {
        if (self::$apiKey === null || self::$partnerId === null) {
            self::markTestSkipped(
                'Integration tests require EFL_API_KEY and EFL_PARTNER_ID environment variables. '
                . 'See sandbox documentation: https://leasingonline-sandbox.efl.com.pl/sandboxDocumentationPage'
            );
        }
    }

    private function createClient(): EflClient
    {
        $config = Config::sandbox(self::$apiKey);
        $guzzle = new Client(['timeout' => 15]);
        $httpClient = new GuzzleHttpAdapter($guzzle);

        return new EflClient($config, $httpClient);
    }

    public function testGetAuthTokenReturnsNonEmptyBearerToken(): void
    {
        $client = $this->createClient();

        $token = $client->getAuthToken(self::$partnerId);

        self::assertNotEmpty($token);
        self::assertMatchesRegularExpression('/^\S+$/', $token, 'Token should be a non-empty string without spaces');
    }

    public function testStartProcessReturnsUrlOrTransactionId(): void
    {
        $client = $this->createClient();
        $token = $client->getAuthToken(self::$partnerId);

        $result = $client->startProcess(
            'https://example.com/success',
            'https://example.com/cancel',
            $token,
        );

        self::assertNotEmpty($result);
        self::assertIsString($result);
    }

    public function testFullFlowGetTokenStartProcessGetBaseData(): void
    {
        $client = $this->createClient();
        $token = $client->getAuthToken(self::$partnerId);

        $initResult = $client->startProcess(
            'https://example.com/success',
            'https://example.com/cancel',
            $token,
        );

        $transactionId = $this->extractTransactionId($initResult);
        self::assertNotEmpty($transactionId, 'Could not extract transactionId from Init response: ' . $initResult);

        $baseData = $client->getBaseData($transactionId, $token);

        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\EsbProcessStatus::class, $baseData->status);
    }

    public function testCalculateBasicOfferReturnsTypedModel(): void
    {
        $client = $this->createClient();
        $token = $client->getAuthToken(self::$partnerId);

        $initResult = $client->startProcess(
            'https://example.com/success',
            'https://example.com/cancel',
            $token,
        );

        $transactionId = $this->extractTransactionId($initResult);
        self::assertNotEmpty($transactionId, 'Could not extract transactionId from Init response');

        $item = new OfferItem(
            count: 1,
            id: 'INT-TEST-1',
            vatRate: 23.0,
            itemDetails: [new ItemDetail('name', 'Integration test product')],
        );
        $basket = new AssetToCalculation($transactionId, [$item]);

        $offer = $client->calculateBasicOffer($basket, $token);

        self::assertSame($transactionId, $offer->transactionId);
        self::assertIsArray($offer->variants);
    }

    public function testInvalidBearerTokenThrowsApiException(): void
    {
        $client = $this->createClient();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('401');

        $client->getBaseData('non-existent-tx-id', 'invalid-bearer-token');
    }

    public function testInvalidTransactionIdThrowsApiException(): void
    {
        $client = $this->createClient();
        $token = $client->getAuthToken(self::$partnerId);

        $this->expectException(ApiException::class);

        $client->getBaseData('invalid-transaction-id-12345', $token);
    }

    /**
     * Extracts transactionId from Process/Init response (URL or JSON).
     */
    private function extractTransactionId(string $initResult): ?string
    {
        $trimmed = trim($initResult);

        if (str_starts_with($trimmed, '{')) {
            $data = json_decode($trimmed, true);
            if (is_array($data) && isset($data['transactionId'])) {
                return (string) $data['transactionId'];
            }
            if (is_array($data) && isset($data['url'])) {
                return $this->extractTransactionIdFromUrl((string) $data['url']);
            }
        }

        return $this->extractTransactionIdFromUrl($trimmed);
    }

    private function extractTransactionIdFromUrl(string $url): ?string
    {
        $parsed = parse_url($url);
        if (!isset($parsed['query'])) {
            return null;
        }

        parse_str($parsed['query'], $params);

        $value = $params['transactionId'] ?? null;
        if ($value === null) {
            return null;
        }

        return is_array($value) ? (string) ($value[0] ?? '') : (string) $value;
    }
}
