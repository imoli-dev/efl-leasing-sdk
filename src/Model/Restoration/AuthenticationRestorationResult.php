<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Restoration;

final class AuthenticationRestorationResult
{
    public ?string $token;

    public ?string $transactionId;

    public ReauthenticationAction $action;

    public function __construct(?string $token, ?string $transactionId, ReauthenticationAction $action)
    {
        $this->token = $token;
        $this->transactionId = $transactionId;
        $this->action = $action;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $actionValue = is_string($data['action'] ?? null) ? $data['action'] : ReauthenticationAction::None->value;

        return new self(
            isset($data['token']) && is_string($data['token']) ? $data['token'] : null,
            isset($data['transactionId']) && is_string($data['transactionId']) ? $data['transactionId'] : null,
            ReauthenticationAction::from($actionValue),
        );
    }
}
