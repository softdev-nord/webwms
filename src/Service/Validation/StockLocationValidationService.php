<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLocationValidationService'
)]
class StockLocationValidationService
{
    /**
     * @param array<mixed> $requestData
     * @returns array<string, mixed>
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     * @SuppressWarnings(ElseExpression)
     */
    public function validateStockLocationData(array $requestData): array
    {
        $responseData = [];

        if (!$requestData['stockLocationLn']) {
            $responseData['error']['stockLocationLn'] = 'Die Lagernummer darf nicht leer sein.';
        } else {
            $responseData['stockLocationLn'] = $requestData['stockLocationLn'];
        }

        if (!$requestData['stockLocationFb']) {
            $responseData['error']['stockLocationFb'] = 'Der Fachboden darf nicht leer sein.';
        } else {
            $responseData['stockLocationFb'] = $requestData['stockLocationFb'];
        }

        if (!$requestData['stockLocationSp']) {
            $responseData['error']['stockLocationSp'] = 'Der Stellplatz darf nicht leer sein.';
        } else {
            $responseData['stockLocationSp'] = $requestData['stockLocationSp'];
        }

        if (!$requestData['stockLocationTf']) {
            $responseData['error']['stockLocationTf'] = 'Die Lagerplatz Tiefe darf nicht leer sein.';
        } else {
            $responseData['stockLocationTf'] = $requestData['stockLocationTf'];
        }

        if (!$requestData['stockLocationDesc']) {
            $responseData['error']['stockLocationDesc'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['stockLocationDesc'] = $requestData['stockLocationDesc'];
        }

        if (!$requestData['stockLocationWidth']) {
            $responseData['error']['stockLocationWidth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['stockLocationWidth'] = $requestData['stockLocationWidth'];
        }

        if (!$requestData['stockLocationDepth']) {
            $responseData['error']['stockLocationDepth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['stockLocationDepth'] = $requestData['stockLocationDepth'];
        }

        if (!$requestData['stockLocationHeight']) {
            $responseData['error']['stockLocationHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['stockLocationHeight'] = $requestData['stockLocationHeight'];
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
