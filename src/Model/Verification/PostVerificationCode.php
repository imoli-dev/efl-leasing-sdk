<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

use Imoli\EflLeasingSdk\Builder\PostVerificationCodeBuilder;

final class PostVerificationCode
{
    public static function builder(): PostVerificationCodeBuilder
    {
        return new PostVerificationCodeBuilder();
    }

    private string $transactionId;

    private string $verificationCode;

    public function __construct(string $transactionId, string $verificationCode)
    {
        $this->transactionId = $transactionId;
        $this->verificationCode = $verificationCode;
    }

    /**
     * @return array<string, string>
     */
    public function toRequestPayload(): array
    {
        return [
            'transactionId' => $this->transactionId,
            'verificationCode' => $this->verificationCode,
        ];
    }
}
