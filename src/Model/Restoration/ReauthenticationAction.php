<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Restoration;

enum ReauthenticationAction: string
{
    case None = 'None';
    case PaymentPending = 'PaymentPending';
    case PaymentAccepted = 'PaymentAccepted';
}
