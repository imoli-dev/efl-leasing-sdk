<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Api;

use Imoli\EflLeasingSdk\Api\LeadApiClient;
use Imoli\EflLeasingSdk\Config;
use Imoli\EflLeasingSdk\Enum\Environment;
use Imoli\EflLeasingSdk\Http\EflHttpClient;
use Imoli\EflLeasingSdk\Model\Lead\ContactData;
use Imoli\EflLeasingSdk\Model\Lead\Prospect;
use Imoli\EflLeasingSdk\Model\Lead\StatementLead;
use Imoli\EflLeasingSdk\Tests\Helper\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class LeadApiClientTest extends TestCase
{
    private function createClient(RecordingHttpClient $recorder): LeadApiClient
    {
        $config = new Config(
            apiKey: 'test-key',
            environment: Environment::Sandbox,
            baseUrl: 'https://api.example.test',
        );

        return new LeadApiClient(new EflHttpClient($config, $recorder));
    }

    public function testSendContactFormBuildsCorrectRequest(): void
    {
        $rec = new RecordingHttpClient();
        $client = $this->createClient($rec);
        $prospect = new Prospect('Jan', 'Kowalski', '123', '00-001', '123456789', 'jan@example.com');
        $contactData = new ContactData($prospect, [new StatementLead(1)]);

        $client->sendContactForm('tx-1', $contactData, 'bearer');

        self::assertSame('POST', $rec->method);
        self::assertSame(
            'https://api.example.test/lon/api/v1/Lead/SendContactForm?transactionId=tx-1',
            $rec->url
        );
        self::assertJsonStringEqualsJsonString(
            json_encode($contactData->toRequestPayload(), JSON_THROW_ON_ERROR),
            (string) $rec->body
        );
    }
}
