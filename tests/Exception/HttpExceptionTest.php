<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Exception;

use Imoli\EflLeasingSdk\Exception\HttpException;
use PHPUnit\Framework\TestCase;

final class HttpExceptionTest extends TestCase
{
    public function testExtendsEflLeasingException(): void
    {
        $e = new HttpException('Connection failed');

        self::assertInstanceOf(\Imoli\EflLeasingSdk\Exception\EflLeasingException::class, $e);
    }

    public function testStoresMessageAndCode(): void
    {
        $e = new HttpException('Timeout', 408);

        self::assertSame('Timeout', $e->getMessage());
        self::assertSame(408, $e->getCode());
    }
}
