<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerValidationService
 */
class SupplierValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string>
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateSupplierData(Supplier $supplier): array
    {
        $responseData = [];

        if (!$supplier->getSupplierNr()) {
            $responseData['error']['supplierNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierNr'] = $supplier->getSupplierNr();
        }

        if (!$supplier->getSupplierName()) {
            $responseData['error']['supplierName'] = 'Der Lieferanten-Name darf nicht leer sein.';
        } else {
            $responseData['supplierName'] = $supplier->getSupplierName();
        }

        if (!$supplier->getSupplierAddressStreet()) {
            $responseData['error']['supplierAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreet'] = $supplier->getSupplierAddressStreet();
        }

        if (!$supplier->getSupplierAddressStreetNr()) {
            $responseData['error']['supplierAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreetNr'] = $supplier->getSupplierAddressStreetNr();
        }

        if (!$supplier->getSupplierAddressCountryCode()) {
            $responseData['error']['supplierAddressCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCountryCode'] = $supplier->getSupplierAddressCountryCode();
        }

        if (!$supplier->getSupplierAddressZipcode()) {
            $responseData['error']['supplierAddressZipcode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['supplierAddressZipcode'] = $supplier->getSupplierAddressZipcode();
        }

        if (!$supplier->getSupplierAddressCity()) {
            $responseData['error']['supplierAddressCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCity'] = $supplier->getSupplierAddressCity();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
