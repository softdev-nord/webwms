<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerValidationService
 */
class CustomerValidationService extends BaseValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, array<string, string>|bool|int|string>
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validateCustomerData(array $requestData): array
    {
        $responseData = [];

        if (!$this->getValue($requestData, '[customerNr]')) {
            $responseData['error']['customerNr'] = 'Die Kunden-Nr. darf nicht leer sein.';
        } else {
            $responseData['customerNr'] = $this->getValue($requestData, '[customerNr]');
        }

        if (!$this->getValue($requestData, '[customerName]')) {
            $responseData['error']['customerName'] = 'Der Kunden-Name darf nicht leer sein.';
        } else {
            $responseData['customerName'] = $this->getValue($requestData, '[customerName]');
        }

        if (!$this->getValue($requestData, '[customerAddressStreet]')) {
            $responseData['error']['customerAddressStreet'] = 'Die Straße darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreet'] = $this->getValue($requestData, '[customerAddressStreet]');
        }

        if (!$this->getValue($requestData, '[customerAddressStreetNr]')) {
            $responseData['error']['customerAddressStreetNr'] = 'Die Hausnummer darf nicht leer sein.';
        } else {
            $responseData['customerAddressStreetNr'] = $this->getValue($requestData, '[customerAddressStreetNr]');
        }

        if (!$this->getValue($requestData, '[customerCountryCode]')) {
            $responseData['error']['customerCountryCode'] = 'Das Land darf nicht leer sein.';
        } else {
            $responseData['customerCountryCode'] = $this->getValue($requestData, '[customerCountryCode]');
        }

        if (!$this->getValue($requestData, '[customerZipCode]')) {
            $responseData['error']['customerZipCode'] = 'Die Postleitzahl darf nicht leer sein.';
        } else {
            $responseData['customerZipCode'] = $this->getValue($requestData, '[customerZipCode]');
        }

        if (!$this->getValue($requestData, '[customerCity]')) {
            $responseData['error']['customerCity'] = 'Die Stadt darf nicht leer sein.';
        } else {
            $responseData['customerCity'] = $this->getValue($requestData, '[customerCity]');
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
