<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\ProcessApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ProcessApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): ProcessApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new ProcessApiClient(new EflHttpClient($config, $recorder));
    }

    public function testGetTokenBuildsCorrectUrlAndUsesApiKey(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getToken('partner-123');

        self::assertSame('GET', $rec->method);
        self::assertSame('https://api.example.test/lon/api/v1/Process/GetToken?partnerId=partner-123', $rec->url);
        self::assertArrayHasKey('ApiKey', $rec->headers);
        self::assertSame('test-key', $rec->headers['ApiKey']);
    }

    public function testInitBuildsCorrectUrlWithOptionalParams(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->init('bearer-xyz', 'https://ok.example', 'https://fail.example');

        self::assertSame('GET', $rec->method);
        self::assertStringContainsString('/lon/api/v1/Process/Init', $rec->url);
        self::assertStringContainsString('PositiveUrlResponse=', $rec->url);
        self::assertStringContainsString('NegativeUrlResponse=', $rec->url);
        self::assertSame('bearer-xyz', $rec->extractedBearerToken);
    }

    public function testGetChangesBuildsCorrectUrlWithStatusBpm(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getChanges('tx-1', ['StatusA', 'StatusB'], 'bearer-tok');

        self::assertSame('GET', $rec->method);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
        self::assertStringContainsString('statusBPM', $rec->url);
        self::assertSame('bearer-tok', $rec->extractedBearerToken);
    }

    public function testGetRestoreProcessBuildsCorrectUrlWithoutAuth(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->getRestoreProcess('tx-restore');

        self::assertSame('GET', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Process/GetRestoreProcess?transactionId=tx-restore',
            $rec->url
        );
        self::assertArrayNotHasKey('Authorization', $rec->headers);
    }

    public function testSetProcessTypeForCompanyBuildsCorrectUrl(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->setProcessTypeForCompany('tx-1', 'bearer', '1234567890', true);

        self::assertSame('POST', $rec->method);
        self::assertStringContainsString('/lon/api/v1/Process/SetProcessTypeForCompany', $rec->url);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
        self::assertStringContainsString('nip=1234567890', $rec->url);
        self::assertStringContainsString('basketCalculation=true', $rec->url);
    }

    public function testSetProcessTypeForCompanyWithoutOptionalParams(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->setProcessTypeForCompany('tx-1', 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertStringContainsString('transactionId=tx-1', $rec->url);
        self::assertStringNotContainsString('nip=', $rec->url);
        self::assertStringNotContainsString('basketCalculation=', $rec->url);
    }

    public function testPostVerificationCodeSendsCorrectPayload(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);

        $client->postVerificationCode(new PostVerificationCode('tx-1', '123456'), 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertSame('https://api.example.test/lon/api/v1/Process/PostVerificationCode', $rec->url);
        self::assertJsonStringEqualsJsonString(
            '{"transactionId":"tx-1","verificationCode":"123456"}',
            (string) $rec->body
        );
    }
}
