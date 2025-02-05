<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\Supplier',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'SupplierService'
)]
readonly class SupplierService
{
    public function __construct(
        private SupplierDataHandler $supplierDataHandler,
    ) {
    }

    public function getSupplierById(int $supplierId): ?Supplier
    {
        return $this->supplierDataHandler->getSupplierById($supplierId);
    }

    public function getSupplierByNr(int $supplierNr): ?Supplier
    {
        return $this->supplierDataHandler->getSupplierByNr($supplierNr);
    }

    public function getAllSuppliers(): JsonResponse
    {
        return new JsonResponse($this->supplierDataHandler->getAllSuppliers());
    }

    public function getAllSuppliersAjax(?string $supplierNrInput): JsonResponse
    {
        return $this->supplierDataHandler->getSuppliers($supplierNrInput);
    }

    public function addSupplier(Supplier $supplier): void
    {
        $this->supplierDataHandler->addSupplier($supplier);
    }

    public function updateSupplier(Supplier $supplier): void
    {
        $this->supplierDataHandler->updateSupplier($supplier);
    }

    public function deleteSupplier(Supplier $supplier): void
    {
        $this->supplierDataHandler->deleteSupplier($supplier);
    }

    public function getLastSupplier(): int
    {
        return $this->supplierDataHandler->getLastSupplier();
    }
}
