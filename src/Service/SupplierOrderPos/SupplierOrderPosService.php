<?php

declare(strict_types=1);

namespace WebWMS\Service\SupplierOrderPos;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderPosEntity;
use WebWMS\Service\DataHandlers\SupplierOrderPos\SupplierOrderPosDataHandler;

/**
 * @package:    WebWMS\Service\SupplierOrderPosEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosService
 */
class SupplierOrderPosService
{
    public function __construct(
        private readonly SupplierOrderPosDataHandler $supplierOrderPosDataHandler
    ) {
    }

    public function getSupplierOrderPosById(int $supplierOrderPosId): ?SupplierOrderPosEntity
    {
        return $this->supplierOrderPosDataHandler->getSupplierOrderPosById($supplierOrderPosId);
    }

    public function getSupplierOrderPosBySupplierOrderId(int $supplierOrderId): ?SupplierOrderPosEntity
    {
        return $this->supplierOrderPosDataHandler->getSupplierOrderPosBySupplierOrderId($supplierOrderId);
    }

    public function getAllSupplierOrderPos(): JsonResponse
    {
        return $this->supplierOrderPosDataHandler->getAllSupplierOrderPos();
    }

    public function addSupplierOrderPos(SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $this->supplierOrderPosDataHandler->addSupplierOrderPos($supplierOrderPosEntity);
    }

    public function updateSupplierOrderPos(SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $this->supplierOrderPosDataHandler->updateSupplierOrderPos($supplierOrderPosEntity);
    }

    public function deleteSupplierOrderPos(?SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $this->supplierOrderPosDataHandler->deleteSupplierOrderPos($supplierOrderPosEntity);
    }
}
