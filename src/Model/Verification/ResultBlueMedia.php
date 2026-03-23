<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

enum ResultBlueMedia: string
{
    case Positive = 'POSITIVE';
    case Negative = 'NEGATIVE';
    case Abandoned = 'ABANDONED';
    case RejectedByUser = 'REJECTED_BY_USER';
}
