<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests;

use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\EflClient;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\ItemDetail;
use Imoli\EflLeasingSdk\Model\Calculation\OfferItem;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;
use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class EflClientTest extends TestCase
{
    public function testGetAuthTokenDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = '  token-xyz  ';
        $client = $this->createClient($http);

        $result = $client->getAuthToken('partner-1');

        self::assertSame('GET', $http->method);
        self::assertStringContainsString('/lon/api/v1/Process/GetToken', $http->url);
        self::assertSame('token-xyz', $result);
    }

    public function testStartProcessDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = 'https://redirect.example.com/start';
        $client = $this->createClient($http);

        $result = $client->startProcess('https://ok.example', 'https://fail.example', 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertStringContainsString('/lon/api/v1/Process/Init', $http->url);
        self::assertSame('https://redirect.example.com/start', $result);
    }

    public function testSubmitCustomerDataDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $company = new \Imoli\EflLeasingSdk\Model\Customer\Company('g1', '123', [], [], [], [], []);
        $customerData = new \Imoli\EflLeasingSdk\Model\Customer\CustomerData('tx-1', 1, $company);
        $client = $this->createClient($http);

        $client->submitCustomerData($customerData, 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertStringContainsString('/lon/api/v1/Customer/PostCustomerDataForLon', $http->url);
    }

    public function testInitializeIdentityVerificationDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'verificationId' => 'v1',
            'redirectUrl' => 'https://verify.example.com',
        ], JSON_THROW_ON_ERROR);
        $params = new \Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams(
            'Jan',
            'Kowalski',
            'ul. Test 1',
            '10',
            '00-001',
            'Warszawa',
            'jan@example.com'
        );
        $client = $this->createClient($http);

        $result = $client->initializeIdentityVerification('tx-1', $params, 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertStringContainsString('/lon/api/v1/Customer/InitializeIdentityVerification', $http->url);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationResult::class, $result);
    }

    public function testGetIdentityVerificationStatusDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'status' => 'PENDING',
            'transactionId' => 'tx-1',
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->getIdentityVerificationStatus('tx-1', 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertStringContainsString('/lon/api/v1/Customer/GetIdentityVerificationStatus', $http->url);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Verification\BlueMediaProcessStateResponse::class, $result);
    }

    public function testGetRestoreProcessChangesDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = '{"changes":[]}';
        $client = $this->createClient($http);

        $result = $client->getRestoreProcessChanges('tx-1');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Process/GetRestoreProcessChanges?transactionId=tx-1',
            $http->url
        );
        self::assertSame('{"changes":[]}', $result);
    }

    public function testSendContactFormDelegatesToLeadApiClient(): void
    {
        $http = new RecordingHttpClient();
        $prospect = new \Imoli\EflLeasingSdk\Model\Lead\Prospect(
            'Jan',
            'Kowalski',
            '1234567890',
            '00-001',
            '123456789',
            'jan@example.com'
        );
        $contactData = new \Imoli\EflLeasingSdk\Model\Lead\ContactData($prospect, []);
        $client = $this->createClient($http);

        $client->sendContactForm('tx-1', $contactData, 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertStringContainsString('/lon/api/v1/Lead', $http->url);
    }

    public function testCalculateBasicOfferReturnsTypedModel(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'transactionId' => 'tx-1',
            'calculationId' => 1,
            'calculationTimestamp' => null,
            'variants' => [],
            'basketCalculation' => false,
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);
        $basket = new AssetToCalculation('tx-1', [new OfferItem(1, 'id', 23.0, [new ItemDetail('name', 'Laptop')])]);

        $result = $client->calculateBasicOffer($basket, 'bearer-token');

        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\EsbCalculateBasicOfferRestReturn::class, $result);
        self::assertSame('tx-1', $result->transactionId);
    }

    public function testCalculateBasicOfferHandlesEmptyResponseBody(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = '';
        $client = $this->createClient($http);
        $basket = new AssetToCalculation('tx-1', [new OfferItem(1, 'id', 23.0, [new ItemDetail('name', 'Laptop')])]);

        $result = $client->calculateBasicOffer($basket, 'bearer-token');

        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\EsbCalculateBasicOfferRestReturn::class, $result);
        self::assertSame('tx-1', $result->transactionId);
        self::assertSame([], $result->variants);
    }

    public function testGetBaseDataDelegatesToCalculationApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'status' => 'Kalkulacja',
            'basket' => null,
            'calculation' => null,
            'calculationVariantId' => null,
            'partnerData' => null,
            'returnToBasketUrl' => null,
            'signProcessRedirectUrl' => null,
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->getBaseData('tx-1', 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Calculation/GetBaseData?transactionId=tx-1', $http->url);
        self::assertSame('bearer-token', $http->extractedBearerToken);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Calculation\CalculationData::class, $result);
    }

    public function testAcceptCalculationDelegatesToCalculationApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $client->acceptCalculation('tx-1', 10, 20, true, 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertStringContainsString('/lon/api/v1/Calculation/AcceptCalculation', $http->url);
        self::assertSame('bearer-token', $http->extractedBearerToken);
    }

    public function testSubmitCustomerStatementsDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $statement = new CustomerDataStatement('guid-1', true, 'id');

        $client->submitCustomerStatements('tx-1', [$statement], 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Customer/PostCustomerStatements?transactionId=tx-1', $http->url);
    }

    public function testInitiatePayByLinkPaymentDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $result = $client->initiatePayByLinkPayment('tx-1', 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Customer/PostInitiatePayByLinkPayment?transactionId=tx-1', $http->url);
        self::assertSame('body', $result);
    }

    public function testGetSectorClassAndTypeDelegatesToProductsApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode(['id' => null, 'feedDate' => null, 'items' => []], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->getSectorClassAndType('tx-1', 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Products/GetSectorClassAndType?transactionId=tx-1', $http->url);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Products\SectorProductInfoTree::class, $result);
    }

    public function testGetBrandModelByProductTypeDelegatesToProductsApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode(['id' => null, 'feedDate' => null, 'items' => []], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->getBrandModelByProductType('tx-1', 5, 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Products/GetBrandModelByProductTypeIdAndPartnerGuid?transactionId=tx-1&productTypeId=5',
            $http->url
        );
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Products\BrandProductInfoTree::class, $result);
    }

    public function testGetProcessChangesDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'transactionId' => 'tx-1',
            'status' => 'Kalkulacja',
            'response' => null,
            'warning' => null,
            'statusWasProcessed' => false,
            'processedResponse' => null,
            'processedStatus' => 'Kalkulacja',
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->getProcessChanges('tx-1', ['Status1', 'Status2'], 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertStringContainsString('/lon/api/v1/Process/GetChanges', $http->url);
        self::assertStringContainsString('transactionId=tx-1', $http->url);
        self::assertStringContainsString('statusBPM=Status1', $http->url);
        self::assertStringContainsString('statusBPM=Status2', $http->url);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Process\FoicProcessStateResponse::class, $result);
    }

    public function testGetRestoreProcessDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'partnerId' => 'partner-1',
            'token' => 'restore-token',
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->getRestoreProcess('tx-1');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Process/GetRestoreProcess?transactionId=tx-1',
            $http->url
        );
        self::assertArrayNotHasKey('Authorization', $http->headers);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Process\AuthenticateResponse::class, $result);
        self::assertSame('partner-1', $result->partnerId);
        self::assertSame('restore-token', $result->token);
    }

    public function testGetLastProcessStatusDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $result = $client->getLastProcessStatus('tx-1', 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Process/GetLastStatus?transactionId=tx-1',
            $http->url
        );
        self::assertSame('body', $result);
    }

    public function testSendItnDelegatesToTestingApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $client->sendItn(['foo' => 'bar'], 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Testing/SendITN', $http->url);
        self::assertSame('bearer-token', $http->extractedBearerToken);
    }

    public function testPostVerificationCodeDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $code = new PostVerificationCode('tx-1', '123456');

        $client->postVerificationCode($code, 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Process/PostVerificationCode', $http->url);
        self::assertSame('bearer-token', $http->extractedBearerToken);
        self::assertJsonStringEqualsJsonString(
            json_encode(['transactionId' => 'tx-1', 'verificationCode' => '123456'], JSON_THROW_ON_ERROR),
            (string) $http->body
        );
    }

    public function testGetPostVerificationCodeChangesDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $result = $client->getPostVerificationCodeChanges('tx-1', 'bearer-token');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Process/GetPostVerificationCodeChanges?transactionId=tx-1',
            $http->url
        );
        self::assertSame('body', $result);
    }

    public function testRestoreCustomerSessionByOrderIdDelegatesToRestorationApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'token' => 't',
            'transactionId' => 'tx-1',
            'action' => 'None',
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->restoreCustomerSessionByOrderId('order-1');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Restoration/RestoreCustomerSession?payByLinkOrderId=order-1',
            $http->url
        );
        self::assertArrayNotHasKey('Authorization', $http->headers);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Restoration\AuthenticationRestorationResult::class, $result);
    }

    public function testRestoreSessionAfterSigningDelegatesToRestorationApiClient(): void
    {
        $http = new RecordingHttpClient();
        $http->responseBody = json_encode([
            'token' => 't',
            'transactionId' => 'tx-1',
            'action' => 'None',
        ], JSON_THROW_ON_ERROR);
        $client = $this->createClient($http);

        $result = $client->restoreSessionAfterSigning('tx-1');

        self::assertSame('GET', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Restoration/RestoreSessionAfterSigning?transactionId=tx-1',
            $http->url
        );
        self::assertArrayNotHasKey('Authorization', $http->headers);
        self::assertInstanceOf(\Imoli\EflLeasingSdk\Model\Restoration\AuthenticationRestorationResult::class, $result);
    }

    public function testSendItnViaCustomerEndpointDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $client->sendItnViaCustomerEndpoint('<xml>transactions</xml>');

        self::assertSame('POST', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Customer/ITN', $http->url);
        self::assertArrayHasKey('Content-Type', $http->headers);
        self::assertStringContainsString('multipart/form-data; boundary=', $http->headers['Content-Type']);
        self::assertStringContainsString('transactions', (string) $http->body);
    }

    public function testSubmitLeadVerificationResultDelegatesToCustomerApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);
        $result = new VerificationResult('OK', 'POSITIVE');

        $client->submitLeadVerificationResult('tx-1', $result, 'bearer-token');

        self::assertSame('POST', $http->method);
        self::assertSame(
            'https://example.test/lon/api/v1/Customer/LeadVerificationResult?transactionId=tx-1',
            $http->url
        );
        self::assertJsonStringEqualsJsonString('{"status":"OK","result":"POSITIVE"}', (string) $http->body);
    }

    public function testSetProcessTypeForCompanyDelegatesToProcessApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $client->setProcessTypeForCompany('tx-1', 'bearer-token', '1234567890', true);

        self::assertSame('POST', $http->method);
        self::assertStringContainsString('/lon/api/v1/Process/SetProcessTypeForCompany', $http->url);
        self::assertStringContainsString('transactionId=tx-1', $http->url);
        self::assertStringContainsString('nip=1234567890', $http->url);
        self::assertStringContainsString('basketCalculation=true', $http->url);
    }

    public function testGetIdentityReturnUrlForDemoDelegatesToDemoApiClient(): void
    {
        $http = new RecordingHttpClient();
        $client = $this->createClient($http);

        $result = $client->getIdentityReturnUrlForDemo();

        self::assertSame('GET', $http->method);
        self::assertSame('https://example.test/lon/api/v1/Demo/GetIdentityReturnUrl', $http->url);
        self::assertSame('body', $result);
    }

    private function createClient(RecordingHttpClient $http): EflClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://example.test',
        );

        return new EflClient($config, $http);
    }
}
