<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Supplier;
use WebWMS\Exception\NotFoundException;
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

    /**
     * @throws NotFoundException
     */
    public function getSupplierApi(int $supplierId): ?Supplier
    {
        $supplier = $this->supplierDataHandler->getSupplierApi($supplierId);

        if (!$supplier) {
            throw new NotFoundException('Supplier with id '.$supplierId.' does not exist!');
        }

        return $supplier;
    }

    public function getAllSuppliersApi(): ?array
    {
        return $this->supplierDataHandler->getAllSuppliersApi();
    }

    public function addSupplierApi(Request $request): Supplier
    {
        $requestData = $request->request->all();

        return $this->supplierDataHandler->addSupplier($requestData);
    }

    public function updateSupplierApi(Request $request): ?Supplier
    {
        $requestData = $request->request->all();

        return $this->supplierDataHandler->updateSupplier($requestData);
    }

    public function getSupplierByNr(int $supplierNr): ?Supplier
    {
        return $this->supplierDataHandler->getSupplierByNr($supplierNr);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function deleteSupplierApi(int $supplierId): void
    {
        $supplier = $this->supplierDataHandler->getSupplierById($supplierId);

        if (!$supplier) {
            throw new NotFoundException('Supplier with id '.$supplierId.' does not exist!');
        } else {
            $this->supplierDataHandler->delete($supplier);
        }
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
