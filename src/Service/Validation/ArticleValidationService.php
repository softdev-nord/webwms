<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\Article;

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
    public function validateArticleData(Article $article): array
    {
        $responseData = [];

        // Validation of the request data from the article data change
        if (!$article->getArticleNr()) {
            $responseData['error']['articleNr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['articleNr'] = $article->getArticleNr();
        }

        if (!$article->getArticleName()) {
            $responseData['error']['articleName'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['articleName'] = $article->getArticleName();
        }

        if (!$article->getArticleCategory()) {
            $responseData['error']['articleCategory'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['articleCategory'] = $article->getArticleCategory();
        }

        if (!$article->getArticleWeight()) {
            $responseData['error']['articleWeight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['articleWeight'] = $article->getArticleWeight();
        }

        if (!$article->getArticleEan()) {
            $responseData['error']['articleEan'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['articleEan'] = $article->getArticleEan();
        }

        if (!$article->getArticleUnit()) {
            $responseData['error']['articleUnit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['articleUnit'] = $article->getArticleUnit();
        }

        if (!$article->getArticleDepth()) {
            $responseData['error']['articleDepth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['articleDepth'] = $article->getArticleDepth();
        }

        if (!$article->getArticleWidth()) {
            $responseData['error']['articleWidth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['articleWidth'] = $article->getArticleWidth();
        }

        if (!$article->getArticleHeight()) {
            $responseData['error']['articleHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['articleHeight'] = $article->getArticleHeight();
        }

        if (!$article->getStockOutStrategy()) {
            $responseData['error']['stockOutStrategy'] = 'Die Auslagerungsstrategie darf nicht leer sein.';
        } else {
            $responseData['stockOutStrategy'] = $article->getStockOutStrategy();
        }

        if (!$article->getStandardLoadingEquipment()) {
            $responseData['error']['standardLoadingEquipment'] = 'Das Standard-Ladehilfsmittel darf nicht leer sein.';
        } else {
            $responseData['standardLoadingEquipment'] = $article->getStandardLoadingEquipment();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
