<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerValidationService
 */
class SupplierValidationService extends BaseValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, array<string, string>|bool|int|string>
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateSupplierData(array $requestData): array
    {
        $responseData = [];

        if (!$this->getValue($requestData, '[supplierNr]')) {
            $responseData['error']['supplierNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierNr'] = $this->getValue($requestData, '[supplierNr]');
        }

        if (!$this->getValue($requestData, '[supplierName]')) {
            $responseData['error']['supplierName'] = 'Der Lieferanten-Name darf nicht leer sein.';
        } else {
            $responseData['supplierName'] = $this->getValue($requestData, '[supplierName]');
        }

        if (!$this->getValue($requestData, '[supplierAddressStreet]')) {
            $responseData['error']['supplierAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreet'] = $this->getValue($requestData, '[supplierAddressStreet]');
        }

        if (!$this->getValue($requestData, '[supplierAddressStreetNr]')) {
            $responseData['error']['supplierAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['supplierAddressStreetNr'] = $this->getValue($requestData, '[supplierAddressStreetNr]');
        }

        if (!$this->getValue($requestData, '[supplierAddressCountryCode]')) {
            $responseData['error']['supplierAddressCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCountryCode'] = $this->getValue($requestData, '[supplierAddressCountryCode]');
        }

        if (!$this->getValue($requestData, '[supplierAddressZipcode]')) {
            $responseData['error']['supplierAddressZipcode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['supplierAddressZipcode'] = $this->getValue($requestData, '[supplierAddressZipcode]');
        }

        if (!$this->getValue($requestData, '[supplierAddressCity]')) {
            $responseData['error']['supplierAddressCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['supplierAddressCity'] = $this->getValue($requestData, '[supplierAddressCity]');
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
