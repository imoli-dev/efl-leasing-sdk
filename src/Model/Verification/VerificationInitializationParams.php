<?php

declare(strict_types=1);

namespace Imoli\EflLeasingSdk\Model\Verification;

use Imoli\EflLeasingSdk\Builder\VerificationInitializationParamsBuilder;

final class VerificationInitializationParams
{
    public static function builder(): VerificationInitializationParamsBuilder
    {
        return new VerificationInitializationParamsBuilder();
    }

    private string $firstName;

    private string $lastName;

    private string $residenceAddressStreet;

    private string $residenceAddressHouseNumber;

    private string $residenceAddressPostalCode;

    private string $residenceAddressCity;

    private string $email;

    public function __construct(
        string $firstName,
        string $lastName,
        string $residenceAddressStreet,
        string $residenceAddressHouseNumber,
        string $residenceAddressPostalCode,
        string $residenceAddressCity,
        string $email,
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->residenceAddressStreet = $residenceAddressStreet;
        $this->residenceAddressHouseNumber = $residenceAddressHouseNumber;
        $this->residenceAddressPostalCode = $residenceAddressPostalCode;
        $this->residenceAddressCity = $residenceAddressCity;
        $this->email = $email;
    }

    /**
     * @return array<string, string>
     */
    public function toRequestPayload(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'residenceAddressStreet' => $this->residenceAddressStreet,
            'residenceAddressHouseNumber' => $this->residenceAddressHouseNumber,
            'residenceAddressPostalCode' => $this->residenceAddressPostalCode,
            'residenceAddressCity' => $this->residenceAddressCity,
            'email' => $this->email,
        ];
    }
}
