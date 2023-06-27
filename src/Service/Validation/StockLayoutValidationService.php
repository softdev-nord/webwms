<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutValidationService
 */
class StockLayoutValidationService
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

        if (!$requestData['stockNr']) {
            $responseData['error']['stockNr'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $requestData['stockNr'];
        }

        if (!$requestData['stockDescription']) {
            $responseData['error']['stockDescription'] = 'Die Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockDescription'] = $requestData['stockDescription'];
        }

        if (!$requestData['stockLevel1']) {
            $responseData['error']['stockLevel1'] = 'Die Ebene 1 darf nicht leer sein.';
        } else {
            $responseData['stockLevel1'] = $requestData['stockLevel1'];
        }

        if (!$requestData['stockLevel2']) {
            $responseData['error']['stockLevel2'] = 'Die Ebene 2 darf nicht leer sein.';
        } else {
            $responseData['stockLevel2'] = $requestData['stockLevel2'];
        }

        if (!$requestData['stockLevel3']) {
            $responseData['error']['stockLevel3'] = 'Die Ebene 3 darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $requestData['stockLevel3'];
        }

        if (!$requestData['stockLevel4']) {
            $responseData['error']['stockLevel4'] = 'Die Ebene 4 darf nicht leer sein.';
        } else {
            $responseData['stockLevel4'] = $requestData['stockLevel4'];
        }

        if (!$requestData['stockModel']) {
            $responseData['error']['stockModel'] = 'Das Lagermodell darf nicht leer sein.';
        } else {
            $responseData['stockModel'] = $requestData['stockModel'];
        }

        if (!$requestData['stockTyp']) {
            $responseData['error']['stockTyp'] = 'Der Lagertyp darf nicht leer sein.';
        } else {
            $responseData['stockTyp'] = $requestData['stockTyp'];
        }

        if (!$requestData['stockLongDescription']) {
            $responseData['error']['stockLongDescription'] = 'Die Lang-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockLongDescription'] = $requestData['stockLongDescription'];
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
