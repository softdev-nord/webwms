<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function getSupplierById(int $supplierId): ?Supplier
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

    public function addSupplier(Request $request): void
    {
        $this->supplierDataHandler->addSupplier($request);
    }

    /**
     * @return object[]
     */
    public function getLastSupplier(): array
    {
        return $this->supplierDataHandler->getLastSupplier();
    }

    public function updateSupplier(Request $request): ?Supplier
    {
        return $this->supplierDataHandler->updateSupplier($request);
    }

    public function deleteSupplier(int $supplierNr): void
    {
        $this->supplierDataHandler->deleteSupplier($supplierNr);
    }
}
