<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerValidationService
 */
class SupplierValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string>
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateSupplierData(Supplier $requestData): array
    {
        $responseData = [];

        if (empty($requestData->getSupplierNr())) {
            $responseData['error']['supplierNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierNr'] = $requestData->getSupplierNr();
        }

        if (empty($requestData->getSupplierName())) {
            $responseData['error']['supplierName'] = 'Der Lieferanten-Name darf nicht leer sein.';
        } else {
            $responseData['supplierName'] = $requestData->getSupplierName();
        }

        if (empty($requestData->getSupplierAddressStreet())) {
            $responseData['error']['supplierAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreet'] = $requestData->getSupplierAddressStreet();
        }

        if (empty($requestData->getSupplierAddressStreetNr())) {
            $responseData['error']['supplierAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreetNr'] = $requestData->getSupplierAddressStreetNr();
        }

        if (empty($requestData->getSupplierAddressCountryCode())) {
            $responseData['error']['supplierAddressCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCountryCode'] = $requestData->getSupplierAddressCountryCode();
        }

        if (empty($requestData->getSupplierAddressZipcode())) {
            $responseData['error']['supplierAddressZipcode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['supplierAddressZipcode'] = $requestData->getSupplierAddressZipcode();
        }

        if (empty($requestData->getSupplierAddressCity())) {
            $responseData['error']['supplierAddressCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCity'] = $requestData->getSupplierAddressCity();
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
