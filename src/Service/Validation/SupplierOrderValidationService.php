<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArticleValidationService
 */
class SupplierOrderValidationService
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateSupplierOrderData($requestData): array
    {
        $responseData = [];

        // Validation of the request data from the supplier order data change
        if (empty($requestData['supplierOrderNr'])) {
            $responseData['error']['supplierOrderNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierOrderNr'] = $requestData['supplierOrderNr'];
        }

        if (empty($requestData['supplierOrderDate'])) {
            $responseData['error']['supplierOrderDate'] = 'Das Bestelldatum darf nicht leer sein.';
        } else {
            $responseData['supplierOrderDate'] = $requestData['supplierOrderDate'];
        }

        if (empty($requestData['supplierOrderCreationDate'])) {
            $responseData['error']['supplierOrderCreationDate'] = 'Das Erstellungsdatum der Bestellung darf nicht leer sein.';
        } else {
            $responseData['supplierOrderCreationDate'] = $requestData['supplierOrderCreationDate'];
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
