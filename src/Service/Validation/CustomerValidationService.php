<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'CustomerValidationService'
)]
class CustomerValidationService
{
    /**
     * @param array<mixed> $requestData
     * @returns array<string, mixed>
     *
     * @SuppressWarnings(ElseExpression)
     * @SuppressWarnings(NPathComplexity)
     */
    public function validateCustomerData(array $requestData): array
    {
        $responseData = [];

        if (!$requestData['customerNr']) {
            $responseData['error']['customerNr'] = 'Die Kunden-Nr. darf nicht leer sein.';
        } else {
            $responseData['customerNr'] = $requestData['customerNr'];
        }

        if (!$requestData['customerName']) {
            $responseData['error']['customerName'] = 'Der Kunden-Name darf nicht leer sein.';
        } else {
            $responseData['customerName'] = $requestData['customerName'];
        }

        if (!$requestData['customerAddressStreet']) {
            $responseData['error']['customerAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreet'] = $requestData['customerAddressStreet'];
        }

        if (!$requestData['customerAddressStreetNr']) {
            $responseData['error']['customerAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreetNr'] = $requestData['customerAddressStreetNr'];
        }

        if (!$requestData['customerCountryCode']) {
            $responseData['error']['customerCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['customerCountryCode'] = $requestData['customerCountryCode'];
        }

        if (!$requestData['customerZipCode']) {
            $responseData['error']['customerZipCode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['customerZipCode'] = $requestData['customerZipCode'];
        }

        if (!$requestData['customerCity']) {
            $responseData['error']['customerCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['customerCity'] = $requestData['customerCity'];
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
