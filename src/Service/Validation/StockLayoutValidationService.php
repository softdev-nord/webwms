<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\StockLayout;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLayoutValidationService
 */
class StockLayoutValidationService
{
    /**
     * @param StockLayout $stockLayout
     * @return array<string, array<string, string>|bool|int|string>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockLayoutData(StockLayout $stockLayout): array
    {
        $responseData = [];

        // Validation of the request data from the stock location data change

        if (empty($stockLayout->getStockNr())) {
            $responseData['error']['stock_location_ln'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $stockLayout->getStockNr();
        }

        if (empty($stockLayout->getStockDescription())) {
            $responseData['error']['stockDescription'] = 'Die Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockDescription'] = $stockLayout->getStockDescription();
        }

        if (empty($stockLayout->getStockLevel1())) {
            $responseData['error']['stockLevel1'] = 'Der Fachboden darf nicht leer sein.';
        } else {
            $responseData['stockLevel1'] = $stockLayout->getStockLevel1();
        }

        if (empty($stockLayout->getStockLevel2())) {
            $responseData['error']['stockLevel2'] = 'Der Stellplatz darf nicht leer sein.';
        } else {
            $responseData['stockLevel2'] = $stockLayout->getStockLevel2();
        }

        if (empty($stockLayout->getStockLevel3())) {
            $responseData['error']['stockLevel3'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $stockLayout->getStockLevel3();
        }

        if (empty($stockLayout->getStockLevel4())) {
            $responseData['error']['stockLevel4'] = 'Die Tiefe 2 darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $stockLayout->getStockLevel4();
        }

        if (empty($stockLayout->getStockModel())) {
            $responseData['error']['stockModel'] = 'Das Lagermodell darf nicht leer sein.';
        } else {
            $responseData['stockModel'] = $stockLayout->getStockModel();
        }

        if (empty($stockLayout->getStockTyp())) {
            $responseData['error']['stockTyp'] = 'Der Lagertyp darf nicht leer sein.';
        } else {
            $responseData['stockTyp'] = $stockLayout->getStockTyp();
        }

        if (empty($stockLayout->getStockLongDescription())) {
            $responseData['error']['stockLongDescription'] = 'Die Lang-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stock_location_width'] = $stockLayout->getStockLongDescription();
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
