<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Supplier;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Supplier;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierDataHandler
 */
class SupplierDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(Supplier $supplier): void
    {
        $this->entityManager->persist($supplier);
        $this->entityManager->flush();
    }

    public function delete(Supplier $supplier): void
    {
        $this->entityManager->remove($supplier);
        $this->entityManager->flush();
    }

    public function getSupplierById(int $supplierId): ?Supplier
    {
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findOneBy(['supplierId' => $supplierId]);
    }

    public function getSupplierByNr(int $supplierNr): ?Supplier
    {
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findOneBy(['supplierNr' => $supplierNr]);
    }

    public function getSuppliers(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();
        $numOfBoxSupplier = !empty(filter_input(INPUT_GET, 'numOfBoxSupplier')) ? filter_input(INPUT_GET, 'numOfBoxSupplier') : '';

        $boxName = match ($numOfBoxSupplier) {
            'supplier_name' => 'supplier_name',
            'supplier_address_addition' => 'supplier_address_addition',
            'supplier_address_street' => 'supplier_address_street',
            'supplier_address_street_nr' => 'supplier_address_street_nr',
            'supplier_address_country_code' => 'supplier_address_country_code',
            'supplier_address_zipcode' => 'supplier_address_zipcode',
            'supplier_address_city' => 'supplier_address_city',
            default => 'supplier_nr',
        };

        $data = [];
        if (!empty(filter_input(INPUT_GET, 'name_supplier'))) {
            $nameSupplier = strtolower(trim(filter_input(INPUT_GET, 'name_supplier')));

            $sql = "SELECT supplier_nr, supplier_name, supplier_address_addition, supplier_address_street,
                            supplier_address_street_nr, supplier_address_country_code, supplier_address_zipcode,
                            supplier_address_city, supplier_id 
                        FROM supplier where LOWER($boxName) LIKE '".$nameSupplier."%'";
            $stmt = $connection->executeQuery($sql);

            while ($rowSupplier = $stmt->fetchAssociative()) {
                $nameSupplier = $rowSupplier['supplier_nr'].'|'.
                    $rowSupplier['supplier_name'].'|'.
                    $rowSupplier['supplier_address_addition'].'|'.
                    $rowSupplier['supplier_address_street'].'|'.
                    $rowSupplier['supplier_address_street_nr'].'|'.
                    $rowSupplier['supplier_address_country_code'].'|'.
                    $rowSupplier['supplier_address_zipcode'].'|'.
                    $rowSupplier['supplier_address_city'].'|'.
                    $rowSupplier['supplier_id'];

                $data[] = $nameSupplier;
            }
        }

        return new JsonResponse($data);
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

    public function addSupplier(Supplier $supplier): void
    {
        $supplier->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($supplier);
    }

    public function updateSupplier(Supplier $supplier): void
    {
        $supplier->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($supplier);
    }

    public function deleteSupplier(Supplier $supplier): void
    {
        $this->delete($supplier);
    }

    /**
     * @return object[]
     */
    public function getLastSupplier(): array
    {
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findBy([], ['supplierNr' => 'DESC'], 1, 0);
    }
}
