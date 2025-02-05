<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'ArticleValidationService'
)]
class ArticleValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, mixed>
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     * @SuppressWarnings(ElseExpression)
     */
    public function validateArticleData(array $requestData): array
    {
        $responseData = [];

        if (!$requestData['articleNr']) {
            $responseData['error']['articleNr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['articleNr'] = $requestData['articleNr'];
        }

        if (!$requestData['articleName']) {
            $responseData['error']['articleName'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['articleName'] = $requestData['articleName'];
        }

        if (!$requestData['articleCategory']) {
            $responseData['error']['articleCategory'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['articleCategory'] = $requestData['articleCategory'];
        }

        if (!$requestData['articleWeight']) {
            $responseData['error']['articleWeight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['articleWeight'] = $requestData['articleWeight'];
        }

        if (!$requestData['articleEan']) {
            $responseData['error']['articleEan'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['articleEan'] = $requestData['articleEan'];
        }

        if (!$requestData['articleUnit']) {
            $responseData['error']['articleUnit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['articleUnit'] = $requestData['articleUnit'];
        }

        if (!$requestData['articleDepth']) {
            $responseData['error']['articleDepth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['articleDepth'] = $requestData['articleDepth'];
        }

        if (!$requestData['articleWidth']) {
            $responseData['error']['articleWidth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['articleWidth'] = $requestData['articleWidth'];
        }

        if (!$requestData['articleHeight']) {
            $responseData['error']['articleHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['articleHeight'] = $requestData['articleHeight'];
        }

        if (!$requestData['stockOutStrategy']) {
            $responseData['error']['stockOutStrategy'] = 'Die Auslagerungsstrategie darf nicht leer sein.';
        } else {
            $responseData['stockOutStrategy'] = $requestData['stockOutStrategy'];
        }

        if (!$requestData['standardLoadingEquipment']) {
            $responseData['error']['standardLoadingEquipment'] = 'Das Standard-Ladehilfsmittel darf nicht leer sein.';
        } else {
            $responseData['standardLoadingEquipment'] = $requestData['standardLoadingEquipment'];
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
