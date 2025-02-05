<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use DateTimeInterface;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'SupplierOrderValidationService'
)]
class SupplierOrderValidationService
{
    /**
     * @return array<string, array<string, string>|bool|DateTimeInterface|string>
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     * @SuppressWarnings(ElseExpression)
     */
    public function validateSupplierOrderData(SupplierOrder $supplierOrder): array
    {
        $responseData = [];

        // Validation of the request data from the supplier order data change
        if ($supplierOrder->getSupplierOrderNr() === '' || $supplierOrder->getSupplierOrderNr() === '0') {
            $responseData['error']['supplierOrderNr'] = 'Die Lieferanten-Nr. darf nicht leer sein.';
        } else {
            $responseData['supplierOrderNr'] = $supplierOrder->getSupplierOrderNr();
        }

        if (!$supplierOrder->getSupplierOrderDate() instanceof DateTimeInterface) {
            $responseData['error']['supplierOrderDate'] = 'Das Bestelldatum darf nicht leer sein.';
        } else {
            $responseData['supplierOrderDate'] = $supplierOrder->getSupplierOrderDate();
        }

        if (!$supplierOrder->getSupplierOrderCreationDate() instanceof DateTimeInterface) {
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
