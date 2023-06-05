<?php

declare(strict_types=1);

namespace WebWMS\Service\SupplierOrder;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Service\DataHandlers\SupplierOrder\SupplierOrderDataHandler;

/**
 * @package:    WebWMS\Service\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderService
 */
class SupplierOrderService
{
    public function __construct(
        private SupplierOrderDataHandler $supplierOrderDataHandler
    ) {
    }

    public function getSupplierOrderById(int $supplierId): ?SupplierOrder
    {
        return $this->supplierOrderDataHandler->getSupplierOrderById($supplierId);
    }

    public function getSupplierOrderByNr(string $supplierNr): ?SupplierOrder
    {
        return $this->supplierOrderDataHandler->getSupplierOrderByNr($supplierNr);
    }

    public function getAllSupplierOrder(): JsonResponse
    {
        return $this->supplierOrderDataHandler->getAllSupplierOrder();
    }

    public function addSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->supplierOrderDataHandler->addSupplierOrder($supplierOrder);
    }

    public function updateSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->supplierOrderDataHandler->updateSupplierOrder($supplierOrder);
    }

    public function deleteSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->supplierOrderDataHandler->deleteSupplierOrder($supplierOrder);
    }

    /**
     * @return array<int, SupplierOrder>
     */
    public function getLastSupplierOrderId(): array
    {
        return $this->supplierOrderDataHandler->getLastSupplierOrderId();
    }
}
