<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use DateTimeInterface;
use WebWMS\Entity\SupplierOrderEntity;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderValidationService
 */
class SupplierOrderValidationService
{
    /**
     * @return array<string, (array<string, string>|bool|DateTimeInterface|string)>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateSupplierOrderData(SupplierOrderEntity $supplierOrderEntity): array
    {
        $responseData = [];

        // Validation of the request data from the supplier order data change
        if ($supplierOrderEntity->getSupplierOrderNr() === '' || $supplierOrderEntity->getSupplierOrderNr() === '0') {
            $responseData['error']['supplierOrderNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierOrderNr'] = $supplierOrderEntity->getSupplierOrderNr();
        }

        if (!$supplierOrderEntity->getSupplierOrderDate() instanceof DateTimeInterface) {
            $responseData['error']['supplierOrderDate'] = 'Das Bestelldatum darf nicht leer sein.';
        } else {
            $responseData['supplierOrderDate'] = $supplierOrderEntity->getSupplierOrderDate();
        }

        if (!$supplierOrderEntity->getSupplierOrderCreationDate() instanceof DateTimeInterface) {
            $responseData['error']['supplierOrderCreationDate'] = 'Das Erstellungsdatum der Bestellung darf nicht leer sein.';
        } else {
            $responseData['supplierOrderCreationDate'] = $supplierOrderEntity->getSupplierOrderCreationDate();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
