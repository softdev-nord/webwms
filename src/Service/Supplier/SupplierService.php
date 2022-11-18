<?php

declare(strict_types=1);

namespace WebWMS\Service\Supplier;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
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
        return $this->getSuppliers();
    }

    /**
     * @throws Exception
     */
    public function getSuppliers(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();

        $numOfBoxSupplier = !empty(filter_input(INPUT_GET, 'numOfBoxSupplier')) ? filter_input(INPUT_GET, 'numOfBoxSupplier') : '';
        $nameSupp = !empty(filter_input(INPUT_GET, 'supplier_nr')) ? strtolower(trim(filter_input(INPUT_GET, 'supplier_nr'))) : '';

        $boxName = 'supplier_nr';

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
            $nameSupp = strtolower(trim(filter_input(INPUT_GET, 'name_supplier')));

            $sqlSupp = "SELECT supplier_nr, supplier_name, supplier_address_addition, supplier_address_street,
                            supplier_address_street_nr, supplier_address_country_code, supplier_address_zipcode,
                            supplier_address_city, supplier_id 
                        FROM supplier where LOWER($boxName) LIKE '".$nameSupp."%'";
            $stmt = $connection->executeQuery($sqlSupp);

            while ($rowSupp = $stmt->fetchAssociative()) {
                $nameSupp = $rowSupp['supplier_nr'].'|'.
                            $rowSupp['supplier_name'].'|'.
                            $rowSupp['supplier_address_addition'].'|'.
                            $rowSupp['supplier_address_street'].'|'.
                            $rowSupp['supplier_address_street_nr'].'|'.
                            $rowSupp['supplier_address_country_code'].'|'.
                            $rowSupp['supplier_address_zipcode'].'|'.
                            $rowSupp['supplier_address_city'].'|'.
                            $rowSupp['supplier_id']
                ;

                $data[] = $nameSupp;
            }
        }

        return new JsonResponse($data);
    }

    public function addNewSupplier(Request $request)
    {
        $createdAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $params = $request->request->all()['supplier'];
        $lastCustomer = $this->getLastSupplier()[0]->toArray();

        $customer = new Supplier();
        $customer->setSupplierId($lastCustomer['supplier_id'] + 1);
        $customer->setSupplierNr($lastCustomer['supplier_nr'] + 1);
        $customer->setSupplierName($params['supplier_name']);
        $customer->setSupplierAddressAddition($params['supplier_address_addition']);
        $customer->setSupplierAddressStreet($params['supplier_address_street']);
        $customer->setSupplierAddressStreetNr($params['supplier_address_street_nr']);
        $customer->setSupplierAddressCountryCode($params['supplier_address_country_code']);
        $customer->setSupplierAddressZipcode($params['supplier_address_zipcode']);
        $customer->setSupplierAddressCity($params['supplier_address_city']);
        $customer->setSupplierCreatedAt($createdAt);

        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    /**
     * Get last supplier.
     */
    public function getLastSupplier(): array
    {
        return $this->entityManager
            ->getRepository(Supplier::class)
            ->findBy([], ['supplier_nr' => 'DESC'], 1, 0);
    }
}
