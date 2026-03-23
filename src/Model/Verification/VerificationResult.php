<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

use Imoli\EflLeasingSdk\Builder\VerificationResultBuilder;

/**
 * Payload for POST /lon/api/v1/Customer/LeadVerificationResult.
 *
 * @see https://leasingonlineapi-sandbox.efl.com.pl/swagger/v1/swagger.json (VerificationResult schema)
 */
final class VerificationResult
{
    public static function builder(): VerificationResultBuilder
    {
        return new VerificationResultBuilder();
    }

    public ?string $status;

    public ?string $result;

    public function __construct(?string $status = null, ?string $result = null)
    {
        $this->status = $status;
        $this->result = $result;
    }

    /**
     * @return array<string, string|null>
     */
    public function toRequestPayload(): array
    {
        $payload = [];

        if ($this->status !== null) {
            $payload['status'] = $this->status;
        }

        if ($this->result !== null) {
            $payload['result'] = $this->result;
        }

        return $payload;
    }
}
