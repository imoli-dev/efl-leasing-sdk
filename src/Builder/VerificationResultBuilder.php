<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Builder;

use Imoli\EflLeasingSdk\Model\Verification\VerificationResult;

/**
 * Fluent builder for VerificationResult model.
 */
final class VerificationResultBuilder
{
    private ?string $status = null;

    private ?string $result = null;

    public function withStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function withResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function build(): VerificationResult
    {
        return new VerificationResult($this->status, $this->result);
    }

    public static function create(): self
    {
        return new self();
    }
}
