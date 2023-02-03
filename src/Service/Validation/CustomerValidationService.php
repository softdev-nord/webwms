<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerValidationService
 */
class CustomerValidationService
{
    /**
     * @param  array<string|int|mixed> $requestData
     * @return array<string>
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateCustomerData(array $requestData): array
    {
        $responseData = [];

        // Validation of the request data from the customer data change
        if (empty($requestData['customerNr'])) {
            $responseData['error']['customerNr'] = 'Die Kunden-Nr. darf nicht leer sein.';
        } else {
            $responseData['customerNr'] = $requestData['customerNr'];
        }

        if (empty($requestData['customerName'])) {
            $responseData['error']['customerName'] = 'Der Kunden-Name darf nicht leer sein.';
        } else {
            $responseData['customerName'] = $requestData['customerName'];
        }

        if (empty($requestData['customerAddressStreet'])) {
            $responseData['error']['customerAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreet'] = $requestData['customerAddressStreet'];
        }

        if (empty($requestData['customerAddressStreetNr'])) {
            $responseData['error']['customerAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreetNr'] = $requestData['customerAddressStreetNr'];
        }

        if (empty($requestData['customerCountryCode'])) {
            $responseData['error']['customerCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['customerCountryCode'] = $requestData['customerCountryCode'];
        }

        if (empty($requestData['customerZipCode'])) {
            $responseData['error']['customerZipCode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['customerZipCode'] = $requestData['customerZipCode'];
        }

        if (empty($requestData['customerCity'])) {
            $responseData['error']['customerCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['customerCity'] = $requestData['customerCity'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
