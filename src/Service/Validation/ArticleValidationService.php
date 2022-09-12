<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

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
        if (empty($requestData['article_nr'])) {
            $responseData['error']['article_nr'] = 'Die Artikel-Nr. darf nicht leer sein.';
        } else {
            $responseData['article_nr'] = $requestData['article_nr'];
        }

        if (empty($requestData['article_name'])) {
            $responseData['error']['article_name'] = 'Die Artikel Bezeichnung darf nicht leer sein.';
        } else {
            $responseData['article_name'] = $requestData['article_name'];
        }

        if (empty($requestData['article_category'])) {
            $responseData['error']['article_category'] = 'Die Artikel Kategorie darf nicht leer sein.';
        } else {
            $responseData['article_category'] = $requestData['article_category'];
        }

        if (empty($requestData['article_weight'])) {
            $responseData['error']['article_weight'] = 'Das Artikel Gewicht darf nicht leer sein.';
        } else {
            $responseData['article_weight'] = $requestData['article_weight'];
        }

        if (empty($requestData['article_ean'])) {
            $responseData['error']['article_ean'] = 'Die EAN-Nummer darf nicht leer sein.';
        } else {
            $responseData['article_ean'] = $requestData['article_ean'];
        }

        if (empty($requestData['article_unit'])) {
            $responseData['error']['article_unit'] = 'Die Einheit darf nicht leer sein.';
        } else {
            $responseData['article_unit'] = $requestData['article_unit'];
        }

        if (empty($requestData['article_depth'])) {
            $responseData['error']['article_depth'] = 'Die Breite darf nicht leer sein.';
        } else {
            $responseData['article_depth'] = $requestData['article_depth'];
        }

        if (empty($requestData['article_width'])) {
            $responseData['error']['article_width'] = 'Die Tiefe darf nicht leer sein.';
        } else {
            $responseData['article_width'] = $requestData['article_width'];
        }

        if (empty($requestData['article_height'])) {
            $responseData['error']['article_height'] = 'Die Höhe darf nicht leer sein.';
        } else {
            $responseData['article_height'] = $requestData['article_height'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
