<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerValidationService
 */
class SupplierValidationService
{
    /**
     * @param  array<string|int|mixed> $requestData
     * @return array<string>
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateSupplierData(array $requestData): array
    {
        $responseData = [];

        if (empty($requestData['supplierNr'])) {
            $responseData['error']['supplierNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierNr'] = $requestData['supplierNr'];
        }

        if (empty($requestData['supplierName'])) {
            $responseData['error']['supplierName'] = 'Der Lieferanten-Name darf nicht leer sein.';
        } else {
            $responseData['supplierName'] = $requestData['supplierName'];
        }

        if (empty($requestData['supplierAddressStreet'])) {
            $responseData['error']['supplierAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreet'] = $requestData['supplierAddressStreet'];
        }

        if (empty($requestData['supplierAddressStreetNr'])) {
            $responseData['error']['supplierAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreetNr'] = $requestData['supplierAddressStreetNr'];
        }

        if (empty($requestData['supplierAddressCountryCode'])) {
            $responseData['error']['supplierAddressCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCountryCode'] = $requestData['supplierAddressCountryCode'];
        }

        if (empty($requestData['supplierAddressZipcode'])) {
            $responseData['error']['supplierAddressZipcode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['supplierAddressZipcode'] = $requestData['supplierAddressZipcode'];
        }

        if (empty($requestData['supplierAddressCity'])) {
            $responseData['error']['supplierAddressCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCity'] = $requestData['supplierAddressCity'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
