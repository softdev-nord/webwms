<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationValidationService
 */
class StockLocationValidationService extends BaseValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, array<string, string>|bool|float|int|string>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockLocationData(array $requestData): array
    {
        $responseData = [];

        if (!$this->getValue($requestData, '[stockLocationLn]')) {
            $responseData['error']['stockLocationLn'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stockLocationLn'] = $this->getValue($requestData, '[stockLocationLn]');
        }

        if (!$this->getValue($requestData, '[stockLocationFb]')) {
            $responseData['error']['stockLocationFb'] = 'Der Fachboden darf nicht leer sein.';
        } else {
            $responseData['stockLocationFb'] = $this->getValue($requestData, '[stockLocationFb]');
        }

        if (!$this->getValue($requestData, '[stockLocationSp]')) {
            $responseData['error']['stockLocationSp'] = 'Der Stellplatz darf nicht leer sein.';
        } else {
            $responseData['stockLocationSp'] = $this->getValue($requestData, '[stockLocationSp]');
        }

        if (!$this->getValue($requestData, '[stockLocationTf]')) {
            $responseData['error']['stockLocationTf'] = 'Die Lagerplatz Tiefe darf nicht leer sein.';
        } else {
            $responseData['stockLocationTf'] = $this->getValue($requestData, '[stockLocationTf]');
        }

        if (!$this->getValue($requestData, '[stockLocationDesc]')) {
            $responseData['error']['stockLocationDesc'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['stockLocationDesc'] = $this->getValue($requestData, '[stockLocationDesc]');
        }

        if (!$this->getValue($requestData, '[stockLocationWidth]')) {
            $responseData['error']['stockLocationWidth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['stockLocationWidth'] = $this->getValue($requestData, '[stockLocationWidth]');
        }

        if (!$this->getValue($requestData, '[stockLocationDepth]')) {
            $responseData['error']['stockLocationDepth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stockLocationDepth'] = $this->getValue($requestData, '[stockLocationDepth]');
        }

        if (!$this->getValue($requestData, '[stockLocationHeight]')) {
            $responseData['error']['stockLocationHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['stockLocationHeight'] = $this->getValue($requestData, '[stockLocationHeight]');
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
