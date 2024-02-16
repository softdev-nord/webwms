<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierEntity;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;

/**
 * @package:    WebWMS\Service\SupplierEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierService
 */
class SupplierService
{
    public function __construct(
        private readonly SupplierDataHandler $supplierDataHandler
    ) {
    }

    public function getSupplierById(int $supplierId): ?SupplierEntity
    {
        return $this->supplierDataHandler->getSupplierById($supplierId);
    }

    public function getSupplierByNr(int $supplierNr): ?SupplierEntity
    {
        return $this->supplierDataHandler->getSupplierByNr($supplierNr);
    }

    public function getAllSuppliers(): JsonResponse
    {
        return new JsonResponse($this->supplierDataHandler->getAllSuppliers());
    }

    public function getAllSuppliersAjax(string|null $supplierNrInput): JsonResponse
    {
        return $this->supplierDataHandler->getSuppliers($supplierNrInput);
    }

    public function addSupplier(SupplierEntity $supplierEntity): void
    {
        $this->supplierDataHandler->addSupplier($supplierEntity);
    }

    public function updateSupplier(SupplierEntity $supplierEntity): void
    {
        $this->supplierDataHandler->updateSupplier($supplierEntity);
    }

    public function deleteSupplier(SupplierEntity $supplierEntity): void
    {
        $this->supplierDataHandler->deleteSupplier($supplierEntity);
    }

    public function getLastSupplier(): int
    {
        return $this->supplierDataHandler->getLastSupplier();
    }
}
