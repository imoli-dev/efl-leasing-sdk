<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Error;

final class ProblemDetails
{
    public ?string $type;

    public ?string $title;

    public ?int $status;

    public ?string $detail;

    public ?string $instance;

    /** @var array<string, mixed> */
    public array $additionalProperties;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->type = isset($data['type']) && is_string($data['type']) ? $data['type'] : null;
        $this->title = isset($data['title']) && is_string($data['title']) ? $data['title'] : null;
        $this->status = isset($data['status']) ? (int) $data['status'] : null;
        $this->detail = isset($data['detail']) && is_string($data['detail']) ? $data['detail'] : null;
        $this->instance = isset($data['instance']) && is_string($data['instance']) ? $data['instance'] : null;

        unset($data['type'], $data['title'], $data['status'], $data['detail'], $data['instance']);
        $this->additionalProperties = $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
