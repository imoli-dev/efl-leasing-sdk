<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Exception;

use Imoli\EflLeasingSdk\Model\Error\ProblemDetails;

/**
 * Represents an error response returned by the EFL Leasing Online API.
 *
 * When possible, this exception carries details parsed from the
 * ProblemDetails payload described in the API specification.
 */
class ApiException extends EflLeasingException
{
    private ?ProblemDetails $problemDetails;

    public function __construct(string $message, int $code = 0, ?ProblemDetails $problemDetails = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->problemDetails = $problemDetails;
    }

    /**
     * Returns parsed ProblemDetails payload when available.
     *
     * @return ProblemDetails|null
     */
    public function getProblemDetails(): ?ProblemDetails
    {
        return $this->problemDetails;
    }
}
