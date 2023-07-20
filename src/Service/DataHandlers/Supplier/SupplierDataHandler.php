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
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierDataHandler
 */
class SupplierDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
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

    /**
     * Get all Customers for Ajax-Request.
     */
    public function getSuppliers(string|null $supplierNrInput): JsonResponse
    {
        $data = [];
        if ($supplierNrInput !== null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('s')
                ->from(Supplier::class, 's')
                ->where('s.supplierNr LIKE :supplier_nr')
                ->setParameter(':supplier_nr', '' . $supplierNrInput . '%');

            $suppliers = $queryBuilder->getQuery()->getArrayResult();

            foreach ($suppliers as $supplier) {
                $nameSupplier = $supplier['supplierNr'] . ' | ' .
                    $supplier['supplierName'] . ' | ' .
                    $supplier['supplierAddressAddition'] . ' | ' .
                    $supplier['supplierAddressStreet'] . ' | ' .
                    $supplier['supplierAddressStreetNr'] . ' | ' .
                    $supplier['supplierAddressCountryCode'] . ' | ' .
                    $supplier['supplierAddressZipcode'] . ' | ' .
                    $supplier['supplierAddressCity'] . ' | ' .
                    $supplier['supplierId'];

                $data[] = $nameSupplier;
            }
        }

        return new JsonResponse($data);
    }

    /*    public function getSuppliers(): JsonResponse
        {
            $connection = $this->entityManager->getConnection();
            $numOfBoxSupplier = filter_input(INPUT_GET, 'numOfBoxSupplier') !== null ? filter_input(INPUT_GET, 'numOfBoxSupplier') : '';

            //dd($numOfBoxSupplier);

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
            if (filter_input(INPUT_GET, 'name_supplier') !== null) {
                $nameSupplier = strtolower(trim(strval(filter_input(INPUT_GET, 'name_supplier'))));

                $sql = "SELECT supplier_nr, supplier_name, supplier_address_addition, supplier_address_street,
                                supplier_address_street_nr, supplier_address_country_code, supplier_address_zipcode,
                                supplier_address_city, supplier_id
                            FROM supplier where LOWER($boxName) LIKE '" . $nameSupplier . "%'";
                $stmt = $connection->executeQuery($sql);

                while ($rowSupplier = $stmt->fetchAssociative()) {
                    $nameSupplier = $rowSupplier['supplier_nr'] . ' | ' .
                        $rowSupplier['supplier_name'] . ' | ' .
                        $rowSupplier['supplier_address_addition'] . ' | ' .
                        $rowSupplier['supplier_address_street'] . ' | ' .
                        $rowSupplier['supplier_address_street_nr'] . ' | ' .
                        $rowSupplier['supplier_address_country_code'] . ' | ' .
                        $rowSupplier['supplier_address_zipcode'] . ' | ' .
                        $rowSupplier['supplier_address_city'] . ' | ' .
                        $rowSupplier['supplier_id'];

                    $data[] = $nameSupplier;
                }
            }

            return new JsonResponse($data);
        }*/

    /**
     * @return array<mixed>
     */
    public function getAllSuppliers(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('c')
            ->from(Supplier::class, 'c')
            ->getQuery()
            ->getArrayResult();
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

    public function getLastSupplier(): int
    {
        $result = $this->entityManager
            ->createQueryBuilder()
            ->select('c.supplierId')
            ->from(Supplier::class, 'c')
            ->addOrderBy('c.supplierId', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getArrayResult();

        return intval($result[0]['supplierId']);
    }
}
