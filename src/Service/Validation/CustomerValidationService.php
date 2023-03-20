<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\Customer;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerValidationService
 */
class CustomerValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string>
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateCustomerData(Customer $customer): array
    {
        $responseData = [];

        // Validation of the request data from the customer data change
        if (!$customer->getCustomerNr()) {
            $responseData['error']['customerNr'] = 'Die Kunden-Nr. darf nicht leer sein.';
        } else {
            $responseData['customerNr'] = $customer->getCustomerNr();
        }

        if (!$customer->getCustomerName()) {
            $responseData['error']['customerName'] = 'Der Kunden-Name darf nicht leer sein.';
        } else {
            $responseData['customerName'] = $customer->getCustomerName();
        }

        if (!$customer->getCustomerAddressStreet()) {
            $responseData['error']['customerAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreet'] = $customer->getCustomerAddressStreet();
        }

        if (!$customer->getCustomerAddressStreetNr()) {
            $responseData['error']['customerAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreetNr'] = $customer->getCustomerAddressStreetNr();
        }

        if (!$customer->getCustomerCountryCode()) {
            $responseData['error']['customerCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['customerCountryCode'] = $customer->getCustomerCountryCode();
        }

        if (!$customer->getCustomerZipCode()) {
            $responseData['error']['customerZipCode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['customerZipCode'] = $customer->getCustomerZipCode();
        }

        if (!$customer->getCustomerCity()) {
            $responseData['error']['customerCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['customerCity'] = $customer->getCustomerCity();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
