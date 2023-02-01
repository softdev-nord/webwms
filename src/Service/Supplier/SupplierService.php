<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierService
 */
class SupplierService
{
    public function __construct(
        private SupplierDataHandler $supplierDataHandler
    ) {
    }

    public function getSupplierByNr(int $supplierNr): ?Supplier
    {
        return $this->supplierDataHandler->getSupplierByNr($supplierNr);
    }

    public function getSupplierById($supplierId): ?Supplier
    {
        return $this->supplierDataHandler->getSupplierById($supplierId);
    }

    /**
     * @throws Exception
     */
    public function getAllSuppliers(): JsonResponse
    {
        return $this->supplierDataHandler->getAllSuppliers();
    }

    /**
     * @throws Exception
     */
    public function getAllSuppliersAjax(): JsonResponse
    {
        return $this->supplierDataHandler->getSuppliers();
    }

    public function addSupplier($requestData): void
    {
        $this->supplierDataHandler->addSupplier($requestData);
    }

    /**
     * Get last supplier.
     */
    public function getLastSupplier(): array
    {
        return $this->supplierDataHandler->getLastSupplier();
    }

    public function updateSupplier($requestData): ?Supplier
    {
        return $this->supplierDataHandler->updateSupplier($requestData);
    }

    public function deleteSupplier(int $supplierNr): void
    {
        $this->supplierDataHandler->deleteSupplier($supplierNr);
    }
}
