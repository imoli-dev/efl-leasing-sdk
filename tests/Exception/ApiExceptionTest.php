<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Exception;

use Imoli\EflLeasingSdk\Exception\ApiException;
use Imoli\EflLeasingSdk\Model\Error\ProblemDetails;
use PHPUnit\Framework\TestCase;

final class ApiExceptionTest extends TestCase
{
    public function testExtendsEflLeasingException(): void
    {
        $e = new ApiException('API error');

        self::assertInstanceOf(\Imoli\EflLeasingSdk\Exception\EflLeasingException::class, $e);
    }

    public function testGetProblemDetailsReturnsNullWhenNotSet(): void
    {
        $e = new ApiException('API error');

        self::assertNull($e->getProblemDetails());
    }

    public function testGetProblemDetailsReturnsSetValue(): void
    {
        $problemDetails = ProblemDetails::fromArray([
            'title' => 'Bad Request',
            'status' => 400,
        ]);
        $e = new ApiException('API error', 400, $problemDetails);

        self::assertSame($problemDetails, $e->getProblemDetails());
        self::assertSame(400, $e->getProblemDetails()->status);
    }
}
