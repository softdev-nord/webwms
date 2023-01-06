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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateArticleData($requestData): array
    {
        $responseData = [];

        // Validation of the request data from the article data change
        if (empty($requestData['articleNr'])) {
            $responseData['error']['articleNr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['articleNr'] = $requestData['articleNr'];
        }

        if (empty($requestData['articleName'])) {
            $responseData['error']['articleName'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['articleName'] = $requestData['articleName'];
        }

        if (empty($requestData['articleCategory'])) {
            $responseData['error']['articleCategory'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['articleCategory'] = $requestData['articleCategory'];
        }

        if (empty($requestData['articleWeight'])) {
            $responseData['error']['articleWeight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['articleWeight'] = $requestData['articleWeight'];
        }

        if (empty($requestData['articleEan'])) {
            $responseData['error']['articleEan'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['articleEan'] = $requestData['articleEan'];
        }

        if (empty($requestData['articleUnit'])) {
            $responseData['error']['articleUnit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['articleUnit'] = $requestData['articleUnit'];
        }

        if (empty($requestData['articleDepth'])) {
            $responseData['error']['articleDepth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['articleDepth'] = $requestData['articleDepth'];
        }

        if (empty($requestData['articleWidth'])) {
            $responseData['error']['articleWidth'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['articleWidth'] = $requestData['articleWidth'];
        }

        if (empty($requestData['articleHeight'])) {
            $responseData['error']['articleHeight'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['articleHeight'] = $requestData['articleHeight'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
