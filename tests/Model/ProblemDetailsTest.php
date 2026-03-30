<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Model;

use Imoli\EflLeasingSdk\Model\Error\ProblemDetails;
use PHPUnit\Framework\TestCase;

final class ProblemDetailsTest extends TestCase
{
    public function testFromArrayParsesFullPayload(): void
    {
        $data = [
            'type' => 'https://example.com/errors/validation',
            'title' => 'Validation Error',
            'status' => 400,
            'detail' => 'Invalid request',
            'instance' => '/api/v1/endpoint',
        ];

        $result = ProblemDetails::fromArray($data);

        self::assertSame('https://example.com/errors/validation', $result->type);
        self::assertSame('Validation Error', $result->title);
        self::assertSame(400, $result->status);
        self::assertSame('Invalid request', $result->detail);
        self::assertSame('/api/v1/endpoint', $result->instance);
        self::assertSame([], $result->additionalProperties);
    }

    public function testFromArrayHandlesMissingFields(): void
    {
        $data = [];

        $result = ProblemDetails::fromArray($data);

        self::assertNull($result->type);
        self::assertNull($result->title);
        self::assertNull($result->status);
        self::assertNull($result->detail);
        self::assertNull($result->instance);
    }

    public function testFromArrayPreservesAdditionalProperties(): void
    {
        $data = [
            'type' => null,
            'errors' => ['field' => ['Required']],
        ];

        $result = ProblemDetails::fromArray($data);

        self::assertArrayHasKey('errors', $result->additionalProperties);
        self::assertSame(['field' => ['Required']], $result->additionalProperties['errors']);
    }
}
