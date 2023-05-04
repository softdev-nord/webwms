<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleValidationService
 */
class ArticleValidationService extends BaseValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, array<string, string>|bool|float|string>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateArticleData(array $requestData): array
    {
        $responseData = [];

        if (!$this->getValue($requestData, '[articleNr]')) {
            $responseData['error']['articleNr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['articleNr'] = $this->getValue($requestData, '[articleNr]');
        }

        if (!$this->getValue($requestData, '[articleName]')) {
            $responseData['error']['articleName'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['articleName'] = $this->getValue($requestData, '[articleName]');
        }

        if (!$this->getValue($requestData, '[articleCategory]')) {
            $responseData['error']['articleCategory'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['articleCategory'] = $this->getValue($requestData, '[articleCategory]');
        }

        if (!$this->getValue($requestData, '[articleWeight]')) {
            $responseData['error']['articleWeight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['articleWeight'] = $this->getValue($requestData, '[articleWeight]');
        }

        if (!$this->getValue($requestData, '[articleEan]')) {
            $responseData['error']['articleEan'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['articleEan'] = $this->getValue($requestData, '[articleEan]');
        }

        if (!$this->getValue($requestData, '[articleUnit]')) {
            $responseData['error']['articleUnit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['articleUnit'] = $this->getValue($requestData, '[articleUnit]');
        }

        if (!$this->getValue($requestData, '[articleDepth]')) {
            $responseData['error']['articleDepth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['articleDepth'] = $this->getValue($requestData, '[articleDepth]');
        }

        if (!$this->getValue($requestData, '[articleWidth]')) {
            $responseData['error']['articleWidth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['articleWidth'] = $this->getValue($requestData, '[articleWidth]');
        }

        if (!$this->getValue($requestData, '[articleHeight]')) {
            $responseData['error']['articleHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['articleHeight'] = $this->getValue($requestData, '[articleHeight]');
        }

        if (!$this->getValue($requestData, '[stockOutStrategy]')) {
            $responseData['error']['stockOutStrategy'] = 'Die Auslagerungsstrategie darf nicht leer sein.';
        } else {
            $responseData['stockOutStrategy'] = $this->getValue($requestData, '[stockOutStrategy]');
        }

        if (!$this->getValue($requestData, '[standardLoadingEquipment]')) {
            $responseData['error']['standardLoadingEquipment'] = 'Das Standard-Ladehilfsmittel darf nicht leer sein.';
        } else {
            $responseData['standardLoadingEquipment'] = $this->getValue($requestData, '[standardLoadingEquipment]');
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
