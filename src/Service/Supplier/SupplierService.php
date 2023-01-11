<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private EntityManagerInterface $entityManager,
        private SupplierDataHandler $supplierDataHandler
    ) {
    }

    public function getSupplierApi(int $supplierId): ?Supplier
    {
        $supplier = $this->entityManager
            ->getRepository(Supplier::class)
            ->find($supplierId);

        if (!$supplier) {
            throw new NotFoundException('Supplier with id '.$supplierId.' does not exist!');
        }

        return $supplier;
    }

    public function getAllSuppliersApi(): ?array
    {
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findAll();
    }

    public function addSupplierApi(
        int $supplierId,
        int $supplierNr,
        string $supplierName,
        string $supplierAddressAddition,
        string $supplierAddressStreet,
        string $supplierAddressStreetNr,
        string $supplierAddressCountryCode,
        string $supplierAddressZipCode,
        string $supplierAddressCity
    ): Supplier {
        $supplier = new Supplier();
        $supplier->setSupplierId($supplierId);
        $supplier->setSupplierNr($supplierNr);
        $supplier->setSupplierName($supplierName);
        $supplier->setSupplierAddressAddition($supplierAddressAddition);
        $supplier->setSupplierAddressStreet($supplierAddressStreet);
        $supplier->setSupplierAddressStreetNr($supplierAddressStreetNr);
        $supplier->setSupplierAddressCountryCode($supplierAddressCountryCode);
        $supplier->setSupplierAddressZipcode($supplierAddressZipCode);
        $supplier->setSupplierAddressCity($supplierAddressCity);
        $this->supplierDataHandler->save($supplier);

        return $supplier;
    }

    public function updateSupplierApi(
        int $supplierMainId,
        int $supplierId,
        int $supplierNr,
        string $supplierName,
        string $supplierAddressAddition,
        string $supplierAddressStreet,
        string $supplierAddressStreetNr,
        string $supplierAddressCountryCode,
        string $supplierAddressZipCode,
        string $supplierAddressCity
    ): ?Supplier {
        $supplier = $this->entityManager
            ->getRepository(Supplier::class)
            ->find($supplierMainId);

        $supplier->setSupplierId($supplierId);
        $supplier->setSupplierNr($supplierNr);
        $supplier->setSupplierName($supplierName);
        $supplier->setSupplierAddressAddition($supplierAddressAddition);
        $supplier->setSupplierAddressStreet($supplierAddressStreet);
        $supplier->setSupplierAddressStreetNr($supplierAddressStreetNr);
        $supplier->setSupplierAddressCountryCode($supplierAddressCountryCode);
        $supplier->setSupplierAddressZipcode($supplierAddressZipCode);
        $supplier->setSupplierAddressCity($supplierAddressCity);
        $this->supplierDataHandler->save($supplier);

        return $supplier;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function deleteSupplierApi(int $supplierId): void
    {
        $supplier = $this->entityManager
            ->getRepository(Supplier::class)
            ->find($supplierId);

        if (!$supplier) {
            throw new NotFoundException('Supplier with id '.$supplierId.' does not exist!');
        } else {
            $this->supplierDataHandler->delete($supplier);
        }
    }

    public function getAllSuppliers(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('supplier');

        $stmt = $queryBuilder->executeQuery();

        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
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
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findBy([], ['supplierNr' => 'DESC'], 1, 0);
    }

    public function getSupplierById($id): ?Supplier
    {
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findOneBy(['id' => $id]);
    }

    public function getSupplierByNr(int $supplierNr): ?Supplier
    {
        return $this->supplierDataHandler->getSupplierByNr($supplierNr);
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
