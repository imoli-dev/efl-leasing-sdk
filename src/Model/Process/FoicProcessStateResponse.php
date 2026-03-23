<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Process;

use Imoli\EflLeasingSdk\Model\Calculation\EsbProcessStatus;

/**
 * Response from GET /lon/api/v1/Process/GetChanges.
 *
 * The response, warning and processedResponse fields hold API-defined structures
 * that vary by process status. The Swagger schema does not define concrete types
 * for these fields, so they remain mixed.
 */
final class FoicProcessStateResponse
{
    public ?string $transactionId;

    public EsbProcessStatus $status;

    /** @var mixed API-defined structure; shape depends on status */
    public $response;

    /** @var mixed API-defined structure; shape depends on status */
    public $warning;

    public bool $statusWasProcessed;

    /** @var mixed API-defined structure; shape depends on processedStatus */
    public $processedResponse;

    public EsbProcessStatus $processedStatus;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->transactionId = isset($data['transactionId']) && is_string($data['transactionId'])
            ? $data['transactionId']
            : null;

        $statusValue = is_string($data['status'] ?? null) ? $data['status'] : EsbProcessStatus::Error->value;
        $self->status = EsbProcessStatus::from($statusValue);

        $self->response = $data['response'] ?? null;
        $self->warning = $data['warning'] ?? null;
        $self->statusWasProcessed = (bool) ($data['statusWasProcessed'] ?? false);
        $self->processedResponse = $data['processedResponse'] ?? null;

        $processedStatusValue = is_string($data['processedStatus'] ?? null)
            ? $data['processedStatus']
            : EsbProcessStatus::Error->value;
        $self->processedStatus = EsbProcessStatus::from($processedStatusValue);

        return $self;
    }
}
