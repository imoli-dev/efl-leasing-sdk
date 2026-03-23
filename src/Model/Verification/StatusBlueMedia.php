<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

enum StatusBlueMedia: string
{
    case Ok = 'OK';
    case Error = 'ERROR';
    case Pending = 'PENDING';
}
