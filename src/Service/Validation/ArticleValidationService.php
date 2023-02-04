<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArticleValidationService
 */
class ArticleValidationService
{
    /**
     * @return array<string, array<string, string>|bool|float|string>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateArticleData(Article $requestData): array
    {
        $responseData = [];

        // Validation of the request data from the article data change
        if (empty($requestData->getArticleNr())) {
            $responseData['error']['articleNr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['articleNr'] = $requestData->getArticleNr();
        }

        if (empty($requestData->getArticleName())) {
            $responseData['error']['articleName'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['articleName'] = $requestData->getArticleName();
        }

        if (empty($requestData->getArticleCategory())) {
            $responseData['error']['articleCategory'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['articleCategory'] = $requestData->getArticleCategory();
        }

        if (empty($requestData->getArticleWeight())) {
            $responseData['error']['articleWeight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['articleWeight'] = $requestData->getArticleWeight();
        }

        if (empty($requestData->getArticleEan())) {
            $responseData['error']['articleEan'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['articleEan'] = $requestData->getArticleEan();
        }

        if (empty($requestData->getArticleUnit())) {
            $responseData['error']['articleUnit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['articleUnit'] = $requestData->getArticleUnit();
        }

        if (empty($requestData->getArticleDepth())) {
            $responseData['error']['articleDepth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['articleDepth'] = $requestData->getArticleDepth();
        }

        if (empty($requestData->getArticleWidth())) {
            $responseData['error']['articleWidth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['articleWidth'] = $requestData->getArticleWidth();
        }

        if (empty($requestData->getArticleHeight())) {
            $responseData['error']['articleHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['articleHeight'] = $requestData->getArticleHeight();
        }

        if (empty($requestData->getStockOutStrategy())) {
            $responseData['error']['stockOutStrategy'] = 'Die Auslagerungsstrategie darf nicht leer sein.';
        } else {
            $responseData['stockOutStrategy'] = $requestData->getStockOutStrategy();
        }

        if (empty($requestData->getStandardLoadingEquipment())) {
            $responseData['error']['standardLoadingEquipment'] = 'Das Standard-Ladehilfsmittel darf nicht leer sein.';
        } else {
            $responseData['standardLoadingEquipment'] = $requestData->getStandardLoadingEquipment();
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
