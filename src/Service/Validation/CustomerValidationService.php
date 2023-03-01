<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\Customer;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerValidationService
 */
class CustomerValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string>
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateCustomerData(Customer $requestData): array
    {
        $responseData = [];

        // Validation of the request data from the customer data change
        if (empty($requestData->getCustomerNr())) {
            $responseData['error']['customerNr'] = 'Die Kunden-Nr. darf nicht leer sein.';
        } else {
            $responseData['customerNr'] = $requestData->getCustomerNr();
        }

        if (empty($requestData->getCustomerName())) {
            $responseData['error']['customerName'] = 'Der Kunden-Name darf nicht leer sein.';
        } else {
            $responseData['customerName'] = $requestData->getCustomerName();
        }

        if (empty($requestData->getCustomerAddressStreet())) {
            $responseData['error']['customerAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreet'] = $requestData->getCustomerAddressStreet();
        }

        if (empty($requestData->getCustomerAddressStreetNr())) {
            $responseData['error']['customerAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreetNr'] = $requestData->getCustomerAddressStreetNr();
        }

        if (empty($requestData->getCustomerCountryCode())) {
            $responseData['error']['customerCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['customerCountryCode'] = $requestData->getCustomerCountryCode();
        }

        if (empty($requestData->getCustomerZipCode())) {
            $responseData['error']['customerZipCode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['customerZipCode'] = $requestData->getCustomerZipCode();
        }

        if (empty($requestData->getCustomerCity())) {
            $responseData['error']['customerCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['customerCity'] = $requestData->getCustomerCity();
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
