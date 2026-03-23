<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

final class BlueMediaProcessStateResponse
{
    private ?string $transactionId;

    private StatusBlueMedia $status;

    private ResultBlueMedia $result;

    public function __construct(?string $transactionId, StatusBlueMedia $status, ResultBlueMedia $result)
    {
        $this->transactionId = $transactionId;
        $this->status = $status;
        $this->result = $result;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $status = StatusBlueMedia::from($data['status'] ?? 'ERROR');
        $result = ResultBlueMedia::from($data['result'] ?? 'NEGATIVE');

        return new self(
            $data['transactionId'] ?? null,
            $status,
            $result,
        );
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getStatus(): StatusBlueMedia
    {
        return $this->status;
    }

    public function getResult(): ResultBlueMedia
    {
        return $this->result;
    }
}
