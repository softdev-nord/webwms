<?php

declare(strict_types=1);

namespace WebWMS\Service\SupplierOrderPos;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\SupplierOrderPos\SupplierOrderPosDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\SupplierOrderPos',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'SupplierOrderPosService'
)]
readonly class SupplierOrderPosService
{
    public function __construct(
        private SupplierOrderPosDataHandler $supplierOrderPosDataHandler,
    ) {
    }

    public function getSupplierOrderPosById(int $supplierOrderPosId): ?SupplierOrderPos
    {
        return $this->supplierOrderPosDataHandler->getSupplierOrderPosById($supplierOrderPosId);
    }

    public function getSupplierOrderPosBySupplierOrderId(int $supplierOrderId): ?SupplierOrderPos
    {
        return $this->supplierOrderPosDataHandler->getSupplierOrderPosBySupplierOrderId($supplierOrderId);
    }

    public function getAllSupplierOrderPos(): JsonResponse
    {
        return $this->supplierOrderPosDataHandler->getAllSupplierOrderPos();
    }

    public function addSupplierOrderPos(SupplierOrderPos $supplierOrderPos): void
    {
        $this->supplierOrderPosDataHandler->addSupplierOrderPos($supplierOrderPos);
    }

    public function updateSupplierOrderPos(SupplierOrderPos $supplierOrderPos): void
    {
        $this->supplierOrderPosDataHandler->updateSupplierOrderPos($supplierOrderPos);
    }

    public function deleteSupplierOrderPos(?SupplierOrderPos $supplierOrderPos): void
    {
        $this->supplierOrderPosDataHandler->deleteSupplierOrderPos($supplierOrderPos);
    }
}
