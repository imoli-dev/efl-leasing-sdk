<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Helper;

use Imoli\EflLeasingSdk\Http\HttpClientInterface;
use Imoli\EflLeasingSdk\Http\HttpResponse;

/**
 * Test double that records HTTP requests for assertions.
 */
final class RecordingHttpClient implements HttpClientInterface
{
    public string $method = '';

    public string $url = '';

    /** @var array<string, string> */
    public array $headers = [];

    public ?string $body = null;

    public ?string $extractedBearerToken = null;

    public string $responseBody = 'body';

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null,
    ): HttpResponse {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;

        if (isset($headers['Authorization']) && str_starts_with($headers['Authorization'], 'Bearer ')) {
            $this->extractedBearerToken = substr($headers['Authorization'], 7);
        }

        return new HttpResponse(200, [], $this->responseBody);
    }
}
