<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Supplier;
use WebWMS\Exception\NotFoundException;
use WebWMS\Service\DataHandlers\Supplier\SupplierDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierService
 */
class SupplierService
{
    public function __construct(
        private SupplierDataHandler $supplierDataHandler,
        private DateTimeService $dateTimeService
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
        $supplier = new Supplier();
        $supplier->setSupplierNr((int) $requestData['supplierNr']);
        $supplier->setSupplierName($requestData['supplierName']);
        $supplier->setSupplierAddressAddition($requestData['supplierAddressAddition']);
        $supplier->setSupplierAddressStreet($requestData['supplierAddressStreet']);
        $supplier->setSupplierAddressStreetNr($requestData['supplierAddressStreetNr']);
        $supplier->setSupplierAddressCountryCode($requestData['supplierAddressCountryCode']);
        $supplier->setSupplierAddressZipcode($requestData['supplierAddressZipcode']);
        $supplier->setSupplierAddressCity($requestData['supplierAddressCity']);
        $supplier->setCreatedAt($this->dateTimeService->createDateTime());
        $this->supplierDataHandler->save($supplier);

        return $supplier;
    }

    public function updateSupplierApi(Request $request): ?Supplier
    {
        $requestData = $request->request->all();

        $supplier = $this->supplierDataHandler->getSupplierByNr($requestData['supplierNr']);

        $supplier->setSupplierNr((int) $requestData['supplierNr']);
        $supplier->setSupplierName($requestData['supplierName']);
        $supplier->setSupplierAddressAddition($requestData['supplierAddressAddition']);
        $supplier->setSupplierAddressStreet($requestData['supplierAddressStreet']);
        $supplier->setSupplierAddressStreetNr($requestData['supplierAddressStreetNr']);
        $supplier->setSupplierAddressCountryCode($requestData['supplierAddressCountryCode']);
        $supplier->setSupplierAddressZipcode($requestData['supplierAddressZipcode']);
        $supplier->setSupplierAddressCity($requestData['supplierAddressCity']);
        $supplier->setUpdatedAt($this->dateTimeService->createDateTime());
        $this->supplierDataHandler->save($supplier);

        return $supplier;
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
