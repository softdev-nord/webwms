<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationValidationService
 */
class StockLocationValidationService
{
    public function validateStockLocationData($requestData): array
    {
        $responseData = [];

        // Validation of the request data from the stock location data change
        if (empty($requestData['stock_location_ln'])) {
            $responseData['error']['stock_location_ln'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stock_location_ln'] = $requestData['stock_location_ln'];
        }

        if (empty($requestData['stock_location_fb'])) {
            $responseData['error']['stock_location_fb'] = 'Der Fachboden darf nicht leer sein.';
        } else {
            $responseData['stock_location_fb'] = $requestData['stock_location_fb'];
        }

        if (empty($requestData['stock_location_sp'])) {
            $responseData['error']['stock_location_sp'] = 'Der Stellplatz darf nicht leer sein.';
        } else {
            $responseData['stock_location_sp'] = $requestData['stock_location_sp'];
        }

        if (empty($requestData['stock_location_tf'])) {
            $responseData['error']['stock_location_tf'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stock_location_tf'] = $requestData['stock_location_tf'];
        }

        if (empty($requestData['stock_location_coordinate'])) {
            $responseData['error']['stock_location_coordinate'] = 'Die Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['stock_location_coordinate'] = $requestData['stock_location_coordinate'];
        }

        if (empty($requestData['stock_location_desc'])) {
            $responseData['error']['stock_location_desc'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['stock_location_desc'] = $requestData['stock_location_desc'];
        }

        if (empty($requestData['stock_location_width'])) {
            $responseData['error']['stock_location_width'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['stock_location_width'] = $requestData['stock_location_width'];
        }

        if (empty($requestData['stock_location_depth'])) {
            $responseData['error']['stock_location_depth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stock_location_depth'] = $requestData['stock_location_depth'];
        }

        if (empty($requestData['stock_location_height'])) {
            $responseData['error']['stock_location_height'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['stock_location_height'] = $requestData['stock_location_height'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
