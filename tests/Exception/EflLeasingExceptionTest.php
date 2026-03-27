<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Tests\Exception;

use Imoli\EflLeasingSdk\Exception\EflLeasingException;
use PHPUnit\Framework\TestCase;

final class EflLeasingExceptionTest extends TestCase
{
    public function testExtendsRuntimeException(): void
    {
        $e = new EflLeasingException('test');

        self::assertInstanceOf(\RuntimeException::class, $e);
    }

    public function testStoresMessageAndCode(): void
    {
        $e = new EflLeasingException('Something failed', 500);

        self::assertSame('Something failed', $e->getMessage());
        self::assertSame(500, $e->getCode());
    }

    public function testStoresPreviousException(): void
    {
        $previous = new \Exception('Original');
        $e = new EflLeasingException('Wrapped', 0, $previous);

        self::assertSame($previous, $e->getPrevious());
    }
}
