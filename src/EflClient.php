<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk;

use Imoli\EflLeasingSdk\Api\CalculationApiClient;
use Imoli\EflLeasingSdk\Api\CustomerApiClient;
use Imoli\EflLeasingSdk\Api\DemoApiClient;
use Imoli\EflLeasingSdk\Api\LeadApiClient;
use Imoli\EflLeasingSdk\Api\ProcessApiClient;
use Imoli\EflLeasingSdk\Api\ProductsApiClient;
use Imoli\EflLeasingSdk\Api\RestorationApiClient;
use Imoli\EflLeasingSdk\Api\TestingApiClient;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Http\HttpClientInterface;
use Imoli\EflLeasingSdk\Http\RequestLoggerInterface;
use Imoli\EflLeasingSdk\Model\Calculation\AssetToCalculation;
use Imoli\EflLeasingSdk\Model\Calculation\CalculationData;
use Imoli\EflLeasingSdk\Model\Calculation\EsbCalculateBasicOfferRestReturn;
use Imoli\EflLeasingSdk\Model\Customer\CustomerData;
use Imoli\EflLeasingSdk\Model\Customer\CustomerDataStatement;
use Imoli\EflLeasingSdk\Model\Lead\ContactData;
use Imoli\EflLeasingSdk\Model\Process\AuthenticateResponse;
use Imoli\EflLeasingSdk\Model\Process\FoicProcessStateResponse;
use Imoli\EflLeasingSdk\Model\Products\BrandProductInfoTree;
use Imoli\EflLeasingSdk\Model\Products\SectorProductInfoTree;
use Imoli\EflLeasingSdk\Model\Restoration\AuthenticationRestorationResult;
use Imoli\EflLeasingSdk\Model\Verification\BlueMediaProcessStateResponse;
use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;
use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationParams;
use Imoli\EflLeasingSdk\Model\Verification\VerificationInitializationResult;
use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;

/**
 * Main entry point for working with the EFL Leasing Online API.
 *
 * This class exposes a high-level, SDK-friendly interface for common
 * ecommerce use cases such as starting a leasing process, calculating offers,
 * sending customer data and tracking process status.
 */
final class EflClient
{
    private EflHttpClient $http;

    private ProcessApiClient $processApi;

    private CalculationApiClient $calculationApi;

    private CustomerApiClient $customerApi;

    private ProductsApiClient $productsApi;

    private LeadApiClient $leadApi;

    private TestingApiClient $testingApi;

    private RestorationApiClient $restorationApi;

    private DemoApiClient $demoApi;

    public function __construct(Config $config, HttpClientInterface $httpClient, ?RequestLoggerInterface $logger = null)
    {
        $this->http = new EflHttpClient($config, $httpClient, $logger);
        $this->processApi = new ProcessApiClient($this->http);
        $this->calculationApi = new CalculationApiClient($this->http);
        $this->customerApi = new CustomerApiClient($this->http);
        $this->productsApi = new ProductsApiClient($this->http);
        $this->leadApi = new LeadApiClient($this->http);
        $this->testingApi = new TestingApiClient($this->http);
        $this->restorationApi = new RestorationApiClient($this->http);
        $this->demoApi = new DemoApiClient($this->http);
    }

    /**
     * Retrieves an authentication token for the given partner id
     * using the ApiKey security scheme.
     */
    public function getAuthToken(string $partnerId): string
    {
        $response = $this->processApi->getToken($partnerId);

        return trim($response->getBody());
    }

    /**
     * Starts a new leasing process and returns the initial redirect URL
     * or process identifier, as provided by the API.
     */
    public function startProcess(
        ?string $positiveUrlResponse,
        ?string $negativeUrlResponse,
        string $bearerToken,
    ): string {
        $response = $this->processApi->init($bearerToken, $positiveUrlResponse, $negativeUrlResponse);

        return trim($response->getBody());
    }

    /**
     * Calculates a basic leasing offer for the given basket.
     */
    public function calculateBasicOffer(AssetToCalculation $basket, string $bearerToken): EsbCalculateBasicOfferRestReturn
    {
        $response = $this->calculationApi->calculateBasicOffer($basket, $bearerToken);

        $body = $response->getBody();

        if ($body === '') {
            return EsbCalculateBasicOfferRestReturn::emptyForTransaction(
                $basket->getTransactionId(),
            );
        }

        /** @var array<string, mixed> $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        return EsbCalculateBasicOfferRestReturn::fromArray($data);
    }

    /**
     * Submits customer company data for the given transaction.
     */
    public function submitCustomerData(CustomerData $customerData, string $bearerToken): void
    {
        $this->customerApi->postCustomerDataForLon($customerData, $bearerToken);
    }

    /**
     * Retrieves calculation base data for the given transaction.
     *
     * Wraps GET /lon/api/v1/Calculation/GetBaseData.
     */
    public function getBaseData(string $transactionId, string $bearerToken): CalculationData
    {
        $response = $this->calculationApi->getBaseData($transactionId, $bearerToken);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return CalculationData::fromArray($data);
    }

    /**
     * Accepts a previously calculated offer for the given transaction.
     *
     * Wraps POST /lon/api/v1/Calculation/AcceptCalculation.
     */
    public function acceptCalculation(
        string $transactionId,
        int $calculationId,
        int $calculationVariantId,
        ?bool $basketCalculation,
        string $bearerToken,
    ): void {
        $this->calculationApi->acceptCalculation(
            $transactionId,
            $calculationId,
            $calculationVariantId,
            $basketCalculation,
            $bearerToken,
        );
    }

    /**
     * Submits additional customer statements for the given transaction.
     *
     * Wraps POST /lon/api/v1/Customer/PostCustomerStatements.
     *
     * @param CustomerDataStatement[] $statements
     */
    public function submitCustomerStatements(
        string $transactionId,
        array $statements,
        string $bearerToken,
    ): void {
        $this->customerApi->postCustomerStatements($transactionId, $statements, $bearerToken);
    }

    /**
     * Initiates pay-by-link payment for the given transaction.
     *
     * Wraps POST /lon/api/v1/Customer/PostInitiatePayByLinkPayment.
     *
     * @return string Raw response body returned by the API.
     */
    public function initiatePayByLinkPayment(string $transactionId, string $bearerToken): string
    {
        $response = $this->customerApi->postInitiatePayByLinkPayment($transactionId, $bearerToken);

        return $response->getBody();
    }

    /**
     * Initialises identity verification via BlueMedia and returns the
     * verification initialisation result.
     */
    public function initializeIdentityVerification(
        string $transactionId,
        VerificationInitializationParams $params,
        string $bearerToken,
    ): VerificationInitializationResult {
        $response = $this->customerApi->initializeIdentityVerification($transactionId, $params, $bearerToken);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return VerificationInitializationResult::fromArray($data);
    }

    /**
     * Returns the current identity verification status for the given transaction.
     */
    public function getIdentityVerificationStatus(string $transactionId, string $bearerToken): BlueMediaProcessStateResponse
    {
        $response = $this->customerApi->getIdentityVerificationStatus($transactionId, $bearerToken);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return BlueMediaProcessStateResponse::fromArray($data);
    }

    /**
     * Submits lead verification result for the given transaction.
     *
     * Wraps POST /lon/api/v1/Customer/LeadVerificationResult.
     */
    public function submitLeadVerificationResult(
        string $transactionId,
        VerificationResult $result,
        string $bearerToken,
    ): void {
        $this->customerApi->leadVerificationResult($transactionId, $result, $bearerToken);
    }

    /**
     * Sends lead/contact data related to the leasing process.
     */
    public function sendContactForm(
        string $transactionId,
        ContactData $contactData,
        string $bearerToken,
    ): void {
        $this->leadApi->sendContactForm($transactionId, $contactData, $bearerToken);
    }

    /**
     * Returns sector, class and product type information for the given transaction.
     *
     * Wraps GET /lon/api/v1/Products/GetSectorClassAndType.
     */
    public function getSectorClassAndType(string $transactionId, string $bearerToken): SectorProductInfoTree
    {
        $response = $this->productsApi->getSectorClassAndType($transactionId, $bearerToken);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return SectorProductInfoTree::fromArray($data);
    }

    /**
     * Returns brand and model information for the given product type in the context of a transaction.
     *
     * Wraps GET /lon/api/v1/Products/GetBrandModelByProductTypeIdAndPartnerGuid.
     */
    public function getBrandModelByProductType(
        string $transactionId,
        int $productTypeId,
        string $bearerToken,
    ): BrandProductInfoTree {
        $response = $this->productsApi->getBrandModelByProductTypeIdAndPartnerGuid(
            $transactionId,
            $productTypeId,
            $bearerToken,
        );

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return BrandProductInfoTree::fromArray($data);
    }

    /**
     * Returns process changes for the given transaction and optional BPM status filters.
     *
     * Wraps GET /lon/api/v1/Process/GetChanges.
     *
     * @param string[]|null $statusBpm
     */
    public function getProcessChanges(
        string $transactionId,
        ?array $statusBpm,
        string $bearerToken,
    ): FoicProcessStateResponse {
        $response = $this->processApi->getChanges($transactionId, $statusBpm, $bearerToken);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return FoicProcessStateResponse::fromArray($data);
    }

    /**
     * Returns restore process data (partner and token) for the given transaction.
     *
     * Wraps GET /lon/api/v1/Process/GetRestoreProcess.
     *
     * This endpoint does not require Bearer authentication per the API specification.
     */
    public function getRestoreProcess(string $transactionId): AuthenticateResponse
    {
        $response = $this->processApi->getRestoreProcess($transactionId);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return AuthenticateResponse::fromArray($data);
    }

    /**
     * Returns restore process changes for the given transaction.
     *
     * Wraps GET /lon/api/v1/Process/GetRestoreProcessChanges.
     *
     * This endpoint does not require Bearer authentication per the API specification.
     */
    public function getRestoreProcessChanges(string $transactionId): string
    {
        $response = $this->processApi->getRestoreProcessChanges($transactionId);

        return $response->getBody();
    }

    /**
     * Sets process type for company for the given transaction.
     *
     * Wraps POST /lon/api/v1/Process/SetProcessTypeForCompany.
     */
    public function setProcessTypeForCompany(
        string $transactionId,
        string $bearerToken,
        ?string $nip = null,
        ?bool $basketCalculation = null,
    ): void {
        $this->processApi->setProcessTypeForCompany($transactionId, $bearerToken, $nip, $basketCalculation);
    }

    /**
     * Returns the last process status for the given transaction.
     *
     * Wraps GET /lon/api/v1/Process/GetLastStatus.
     */
    public function getLastProcessStatus(string $transactionId, string $bearerToken): string
    {
        $response = $this->processApi->getLastStatus($transactionId, $bearerToken);

        return $response->getBody();
    }

    /**
     * Sends an ITN notification payload to the EFL sandbox testing endpoint.
     *
     * Wraps POST /lon/api/v1/Testing/SendITN.
     *
     * @param array<string, mixed> $transactionListFlattened
     */
    public function sendItn(array $transactionListFlattened, string $bearerToken): void
    {
        $this->testingApi->sendItn($transactionListFlattened, $bearerToken);
    }

    /**
     * Posts a verification code for the given transaction.
     *
     * Wraps POST /lon/api/v1/Process/PostVerificationCode.
     */
    public function postVerificationCode(PostVerificationCode $code, string $bearerToken): void
    {
        $this->processApi->postVerificationCode($code, $bearerToken);
    }

    /**
     * Returns post-verification-code process changes for the given transaction.
     *
     * Wraps GET /lon/api/v1/Process/GetPostVerificationCodeChanges.
     */
    public function getPostVerificationCodeChanges(string $transactionId, string $bearerToken): string
    {
        $response = $this->processApi->getPostVerificationCodeChanges($transactionId, $bearerToken);

        return $response->getBody();
    }

    /**
     * Restores customer session based on the pay-by-link order identifier.
     *
     * Wraps GET /lon/api/v1/Restoration/RestoreCustomerSession.
     */
    public function restoreCustomerSessionByOrderId(string $payByLinkOrderId): AuthenticationRestorationResult
    {
        $response = $this->restorationApi->restoreCustomerSession($payByLinkOrderId);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return AuthenticationRestorationResult::fromArray($data);
    }

    /**
     * Restores customer session after signing for the given transaction.
     *
     * Wraps GET /lon/api/v1/Restoration/RestoreSessionAfterSigning.
     */
    public function restoreSessionAfterSigning(string $transactionId): AuthenticationRestorationResult
    {
        $response = $this->restorationApi->restoreSessionAfterSigning($transactionId);

        /** @var array<string, mixed> $data */
        $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return AuthenticationRestorationResult::fromArray($data);
    }

    /**
     * Sends ITN payload via the Customer ITN endpoint.
     *
     * Wraps POST /lon/api/v1/Customer/ITN.
     */
    public function sendItnViaCustomerEndpoint(string $transactionsPayload): void
    {
        $this->customerApi->postItn($transactionsPayload);
    }

    /**
     * Returns the identity verification return URL used in demo scenarios.
     *
     * Wraps GET /lon/api/v1/Demo/GetIdentityReturnUrl.
     */
    public function getIdentityReturnUrlForDemo(): string
    {
        $response = $this->demoApi->getIdentityReturnUrl();

        return $response->getBody();
    }
}
