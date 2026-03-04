<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Enum;

/**
 * SDK environment selector.
 *
 * This enum is used to distinguish between sandbox and production
 * environments of the EFL Leasing Online API.
 */
enum Environment: string
{
    case Sandbox = 'sandbox';
    case Production = 'production';
}
