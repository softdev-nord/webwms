<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\ArticleEntity;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleValidationService
 */
class ArticleValidationService
{
    /**
     * @return array<string, array<string, string>|bool|float|string>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateArticleData(ArticleEntity $articleEntity): array
    {
        $responseData = [];

        if ($articleEntity->getArticleNr() === '' || $articleEntity->getArticleNr() === '0') {
            $responseData['error']['articleNr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['articleNr'] = $articleEntity->getArticleNr();
        }

        if ($articleEntity->getArticleName() === '' || $articleEntity->getArticleName() === '0') {
            $responseData['error']['articleName'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['articleName'] = $articleEntity->getArticleName();
        }

        if ($articleEntity->getArticleCategory() === '' || $articleEntity->getArticleCategory() === '0') {
            $responseData['error']['articleCategory'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['articleCategory'] = $articleEntity->getArticleCategory();
        }

        if ($articleEntity->getArticleWeight() === 0.0) {
            $responseData['error']['articleWeight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['articleWeight'] = $articleEntity->getArticleWeight();
        }

        if ($articleEntity->getArticleEan() === '' || $articleEntity->getArticleEan() === '0') {
            $responseData['error']['articleEan'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['articleEan'] = $articleEntity->getArticleEan();
        }

        if ($articleEntity->getArticleUnit() === '' || $articleEntity->getArticleUnit() === '0') {
            $responseData['error']['articleUnit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['articleUnit'] = $articleEntity->getArticleUnit();
        }

        if ($articleEntity->getArticleDepth() === 0.0) {
            $responseData['error']['articleDepth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['articleDepth'] = $articleEntity->getArticleDepth();
        }

        if ($articleEntity->getArticleWidth() === 0.0) {
            $responseData['error']['articleWidth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['articleWidth'] = $articleEntity->getArticleWidth();
        }

        if ($articleEntity->getArticleHeight() === 0.0) {
            $responseData['error']['articleHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['articleHeight'] = $articleEntity->getArticleHeight();
        }

        if ($articleEntity->getStockOutStrategy() === '' || $articleEntity->getStockOutStrategy() === '0') {
            $responseData['error']['stockOutStrategy'] = 'Die Auslagerungsstrategie darf nicht leer sein.';
        } else {
            $responseData['stockOutStrategy'] = $articleEntity->getStockOutStrategy();
        }

        if ($articleEntity->getStandardLoadingEquipment() === '' || $articleEntity->getStandardLoadingEquipment() === '0') {
            $responseData['error']['standardLoadingEquipment'] = 'Das Standard-Ladehilfsmittel darf nicht leer sein.';
        } else {
            $responseData['standardLoadingEquipment'] = $articleEntity->getStandardLoadingEquipment();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
