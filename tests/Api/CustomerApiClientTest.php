<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\CustomerApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Model\Customer\Company;
use Imoli\EflLeasingSdk\Model\Customer\CustomerData;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;
use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class CustomerApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): CustomerApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new CustomerApiClient(new EflHttpClient($config, $recorder));
    }

    public function testPostCustomerDataForLonBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $company = new Company('g1', '123', [], [], [], [], []);
        $customerData = new CustomerData('tx-1', 1, $company);

        $client->postCustomerDataForLon($customerData, 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertStringContainsString('/lon/api/v1/Customer/PostCustomerDataForLon', $rec->url);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
    }

    public function testPostCustomerStatementsBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $stmt = new CustomerDataStatement('guid-1', true, 'id');

        $client->postCustomerStatements('tx-1', [$stmt], 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Customer/PostCustomerStatements?transactionId=tx-1',
            $rec->url
        );
    }

    public function testInitializeIdentityVerificationBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $params = new VerificationInitializationParams(
            'Jan',
            'Kowalski',
            'ul. Test 1',
            '10',
            '00-001',
            'Warszawa',
            'jan@example.com'
        );

        $client->initializeIdentityVerification('tx-1', $params, 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertStringContainsString('/lon/api/v1/Customer/InitializeIdentityVerification', $rec->url);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
    }

    public function testLeadVerificationResultBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $result = new VerificationResult('OK', 'POSITIVE');

        $client->leadVerificationResult('tx-1', $result, 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Customer/LeadVerificationResult?transactionId=tx-1',
            $rec->url
        );
        self::assertJsonStringEqualsJsonString('{"status":"OK","result":"POSITIVE"}', (string) $rec->body);
    }

    public function testPostInitiatePayByLinkPaymentBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->postInitiatePayByLinkPayment('tx-1', 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Customer/PostInitiatePayByLinkPayment?transactionId=tx-1',
            $rec->url
        );
        self::assertSame('bearer', $rec->extractedBearerToken);
    }

    public function testPostItnSendsMultipartFormData(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->postItn('<xml>transactions</xml>');

        self::assertSame('POST', $rec->method);
        self::assertSame('https://api.example.test/lon/api/v1/Customer/ITN', $rec->url);
        self::assertStringContainsString('multipart/form-data', $rec->headers['Content-Type'] ?? '');
        self::assertStringContainsString('transactions', (string) $rec->body);
    }
}
