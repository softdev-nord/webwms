<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\SupplierOrder;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderValidationService
 */
class SupplierOrderValidationService
{
    /**
     * @return array<string, array<string, string>|bool|\DateTimeInterface|string>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateSupplierOrderData(SupplierOrder $supplierOrder): array
    {
        $responseData = [];

        // Validation of the request data from the supplier order data change
        if (!$supplierOrder->getSupplierOrderNr()) {
            $responseData['error']['supplierOrderNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierOrderNr'] = $supplierOrder->getSupplierOrderNr();
        }

        if (!$supplierOrder->getSupplierOrderDate()) {
            $responseData['error']['supplierOrderDate'] = 'Das Bestelldatum darf nicht leer sein.';
        } else {
            $responseData['supplierOrderDate'] = $supplierOrder->getSupplierOrderDate();
        }

        if (!$supplierOrder->getSupplierOrderCreationDate()) {
            $responseData['error']['supplierOrderCreationDate'] = 'Das Erstellungsdatum der Bestellung darf nicht leer sein.';
        } else {
            $responseData['supplierOrderCreationDate'] = $supplierOrder->getSupplierOrderCreationDate();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
