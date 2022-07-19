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
    public function validateCustomerData($requestData): array
    {
        $responseData = [];

        // Validation of the request data from the customer data change
        if (empty($requestData['customer_nr'])) {
            $responseData['error']['customer_nr'] = 'Die Kunden-Nr. darf nicht leer sein.';
        } else {
            $responseData['customer_nr'] = $requestData['customer_nr'];
        }

        if (empty($requestData['customer_name'])) {
            $responseData['error']['customer_name'] = 'Der Kunden-Name darf nicht leer sein.';
        } else {
            $responseData['customer_name'] = $requestData['customer_name'];
        }

        if (empty($requestData['customer_address_street'])) {
            $responseData['error']['customer_address_street'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['customer_address_street'] = $requestData['customer_address_street'];
        }

        if (empty($requestData['customer_address_street_nr'])) {
            $responseData['error']['customer_address_street_nr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['customer_address_street_nr'] = $requestData['customer_address_street_nr'];
        }

        if (empty($requestData['customer_country_code'])) {
            $responseData['error']['customer_country_code'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['customer_country_code'] = $requestData['customer_country_code'];
        }

        if (empty($requestData['customer_zip_code'])) {
            $responseData['error']['customer_zip_code'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['customer_zip_code'] = $requestData['customer_zip_code'];
        }

        if (empty($requestData['customer_city'])) {
            $responseData['error']['customer_city'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['customer_city'] = $requestData['customer_city'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
