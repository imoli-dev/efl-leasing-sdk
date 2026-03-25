<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Verification\PostVerificationCode;

/**
 * Fluent builder for PostVerificationCode model.
 */
final class PostVerificationCodeBuilder
{
    private ?string $transactionId = null;

    private ?string $verificationCode = null;

    public function withTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function withVerificationCode(string $verificationCode): self
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function build(): PostVerificationCode
    {
        if ($this->transactionId === null || $this->verificationCode === null) {
            throw new \LogicException('transactionId and verificationCode are required to build PostVerificationCode');
        }

        return new PostVerificationCode($this->transactionId, $this->verificationCode);
    }

    public static function create(string $transactionId, string $verificationCode): self
    {
        return (new self())
            ->withTransactionId($transactionId)
            ->withVerificationCode($verificationCode);
    }
}
