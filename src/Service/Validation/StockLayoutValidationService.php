<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\StockLayout;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutValidationService
 */
class StockLayoutValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string|null>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockLayoutData(StockLayout $stockLayout): array
    {
        $responseData = [];

        // Validation of the request data from the stock location data change
        if (!$stockLayout->getStockNr()) {
            $responseData['error']['stock_location_ln'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $stockLayout->getStockNr();
        }

        if (!$stockLayout->getStockDescription()) {
            $responseData['error']['stockDescription'] = 'Die Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockDescription'] = $stockLayout->getStockDescription();
        }

        if (!$stockLayout->getStockLevel1()) {
            $responseData['error']['stockLevel1'] = 'Der Fachboden darf nicht leer sein.';
        } else {
            $responseData['stockLevel1'] = $stockLayout->getStockLevel1();
        }

        if (!$stockLayout->getStockLevel2()) {
            $responseData['error']['stockLevel2'] = 'Der Stellplatz darf nicht leer sein.';
        } else {
            $responseData['stockLevel2'] = $stockLayout->getStockLevel2();
        }

        if (!$stockLayout->getStockLevel3()) {
            $responseData['error']['stockLevel3'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $stockLayout->getStockLevel3();
        }

        if (!$stockLayout->getStockLevel4()) {
            $responseData['error']['stockLevel4'] = 'Die Tiefe 2 darf nicht leer sein.';
        } else {
            $responseData['stockLevel3'] = $stockLayout->getStockLevel4();
        }

        if (!$stockLayout->getStockModel()) {
            $responseData['error']['stockModel'] = 'Das Lagermodell darf nicht leer sein.';
        } else {
            $responseData['stockModel'] = $stockLayout->getStockModel();
        }

        if (!$stockLayout->getStockTyp()) {
            $responseData['error']['stockTyp'] = 'Der Lagertyp darf nicht leer sein.';
        } else {
            $responseData['stockTyp'] = $stockLayout->getStockTyp();
        }

        if (!$stockLayout->getStockLongDescription()) {
            $responseData['error']['stockLongDescription'] = 'Die Lang-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stock_location_width'] = $stockLayout->getStockLongDescription();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
