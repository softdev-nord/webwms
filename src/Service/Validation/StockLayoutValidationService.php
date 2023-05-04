<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutValidationService
 */
class StockLayoutValidationService extends BaseValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, array<string, string>|bool|int|string|null>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockLayoutData(array $requestData): array
    {
        $responseData = [];

        if (!$this->getValue($requestData, '[stockNr]')) {
            $responseData['error']['stockNr'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $this->getValue($requestData, '[stockNr]');
        }

        if (!$this->getValue($requestData, '[stockDescription]')) {
            $responseData['error']['stockDescription'] = 'Die Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockDescription'] = $this->getValue($requestData, '[stockDescription]');
        }

        if (!$this->getValue($requestData, '[stockLevel1]')) {
            $responseData['error']['stockLevel1'] = 'Die Ebene 1 darf nicht leer sein.';
        } else {
            $responseData['stockLevel1'] = $this->getValue($requestData, '[stockLevel1]');
        }

        if (!$this->getValue($requestData, '[stockLevel2]')) {
            $responseData['error']['stockLevel2'] = 'Die Ebene 2 darf nicht leer sein.';
        } else {
            $responseData['stockLevel2'] = $this->getValue($requestData, '[stockLevel2]');
        }

        if (!$this->getValue($requestData, '[stockLevel3]')) {
            $responseData['error']['stockLevel3'] = 'Die Ebene 3 darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $this->getValue($requestData, '[stockLevel3]');
        }

        if (!$this->getValue($requestData, '[stockLevel4]')) {
            $responseData['error']['stockLevel4'] = 'Die Ebene 4 darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $this->getValue($requestData, '[stockLevel4]');
        }

        if (!$this->getValue($requestData, '[stockModel]')) {
            $responseData['error']['stockModel'] = 'Das Lagermodell darf nicht leer sein.';
        } else {
            $responseData['stockModel'] = $this->getValue($requestData, '[stockModel]');
        }

        if (!$this->getValue($requestData, '[stockTyp]')) {
            $responseData['error']['stockTyp'] = 'Der Lagertyp darf nicht leer sein.';
        } else {
            $responseData['stockTyp'] = $this->getValue($requestData, '[stockTyp]');
        }

        if (!$this->getValue($requestData, '[stockLongDescription]')) {
            $responseData['error']['stockLongDescription'] = 'Die Lang-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stock_location_width'] = $this->getValue($requestData, '[stockLongDescription]');
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
