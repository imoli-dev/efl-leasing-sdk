<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Process;

/**
 * Response from GET /lon/api/v1/Process/GetRestoreProcess.
 *
 * Contains partner and token data used to restore an existing process session.
 */
final class AuthenticateResponse
{
    public ?string $partnerId;

    public ?string $token;

    public function __construct(?string $partnerId, ?string $token)
    {
        $this->partnerId = $partnerId;
        $this->token = $token;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['partnerId']) && is_string($data['partnerId']) ? $data['partnerId'] : null,
            isset($data['token']) && is_string($data['token']) ? $data['token'] : null,
        );
    }
}
