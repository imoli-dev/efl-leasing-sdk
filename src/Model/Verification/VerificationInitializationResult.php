<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

final class VerificationInitializationResult
{
    private ?string $status;

    private ?string $description;

    private ?string $redirectUrl;

    private ?string $orderUuid;

    public function __construct(
        ?string $status,
        ?string $description,
        ?string $redirectUrl,
        ?string $orderUuid,
    ) {
        $this->status = $status;
        $this->description = $description;
        $this->redirectUrl = $redirectUrl;
        $this->orderUuid = $orderUuid;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['status'] ?? null,
            $data['description'] ?? null,
            $data['redirectUrl'] ?? null,
            $data['orderUuid'] ?? null,
        );
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getOrderUuid(): ?string
    {
        return $this->orderUuid;
    }
}
