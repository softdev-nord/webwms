<?php

declare(strict_types=1);

namespace WebWMS\Service\SupplierOrder;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Service\DataHandlers\SupplierOrder\SupplierOrderDataHandler;

/**
 * @package:    WebWMS\Service\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderService
 */
class SupplierOrderService
{
    public function __construct(
        private readonly SupplierOrderDataHandler $supplierOrderDataHandler
    ) {
    }

    public function getSupplierOrderById(int $supplierId): ?SupplierOrderEntity
    {
        return $this->supplierOrderDataHandler->getSupplierOrderById($supplierId);
    }

    public function getSupplierOrderByNr(string $supplierNr): ?SupplierOrderEntity
    {
        return $this->supplierOrderDataHandler->getSupplierOrderByNr($supplierNr);
    }

    public function getAllSupplierOrder(): JsonResponse
    {
        return $this->supplierOrderDataHandler->getAllSupplierOrder();
    }

    public function addSupplierOrder(SupplierOrderEntity $supplierOrderEntity): void
    {
        $this->supplierOrderDataHandler->addSupplierOrder($supplierOrderEntity);
    }

    public function updateSupplierOrder(SupplierOrderEntity $supplierOrderEntity): void
    {
        $this->supplierOrderDataHandler->updateSupplierOrder($supplierOrderEntity);
    }

    public function deleteSupplierOrder(SupplierOrderEntity $supplierOrderEntity): void
    {
        $this->supplierOrderDataHandler->deleteSupplierOrder($supplierOrderEntity);
    }

    /**
     * @return array<int, SupplierOrderEntity>
     */
    public function getLastSupplierOrderId(): array
    {
        return $this->supplierOrderDataHandler->getLastSupplierOrderId();
    }
}
