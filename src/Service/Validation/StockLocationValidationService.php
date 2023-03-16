<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationValidationService
 */
class StockLocationValidationService
{
    /**
     * @return array<string, array<string, string>|bool|float|int|string>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockLocationData(StockLocation $stockLocation): array
    {
        $responseData = [];

        // Validation of the request data from the stock location data change
        if (!$stockLocation->getStockLocationLn()) {
            $responseData['error']['stock_location_ln'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stock_location_ln'] = $stockLocation->getStockLocationLn();
        }

        if (!$stockLocation->getStockLocationFb()) {
            $responseData['error']['stock_location_fb'] = 'Der Fachboden darf nicht leer sein.';
        } else {
            $responseData['stock_location_fb'] = $stockLocation->getStockLocationFb();
        }

        if (!$stockLocation->getStockLocationSp()) {
            $responseData['error']['stock_location_sp'] = 'Der Stellplatz darf nicht leer sein.';
        } else {
            $responseData['stock_location_sp'] = $stockLocation->getStockLocationSp();
        }

        if (!$stockLocation->getStockLocationTf()) {
            $responseData['error']['stock_location_tf'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stock_location_tf'] = $stockLocation->getStockLocationTf();
        }

        if (!$stockLocation->getStockLocationCoordinate()) {
            $responseData['error']['stock_location_coordinate'] = 'Die Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['stock_location_coordinate'] = $stockLocation->getStockLocationCoordinate();
        }

        if (!$stockLocation->getStockLocationDesc()) {
            $responseData['error']['stock_location_desc'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['stock_location_desc'] = $stockLocation->getStockLocationDesc();
        }

        if (!$stockLocation->getStockLocationWidth()) {
            $responseData['error']['stock_location_width'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['stock_location_width'] = $stockLocation->getStockLocationWidth();
        }

        if (!$stockLocation->getStockLocationDepth()) {
            $responseData['error']['stock_location_depth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stock_location_depth'] = $stockLocation->getStockLocationDepth();
        }

        if (!$stockLocation->getStockLocationHeight()) {
            $responseData['error']['stock_location_height'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['stock_location_height'] = $stockLocation->getStockLocationHeight();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
