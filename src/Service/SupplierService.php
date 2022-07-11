<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Supplier;
use WebWMS\Exception\NotFoundException;
use WebWMS\Repository\SupplierRepository;

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
        private SupplierRepository $supplierRepository
    ) {
    }

    public function getSupplierApi(int $supplierId): ?Supplier
    {
        $supplier = $this->entityManager->getRepository(Supplier::class)->findById($supplierId);

        if (!$supplier) {
            throw new NotFoundException(
                'Supplier with id '.$supplierId.' does not exist!'
            );
        }

        return $supplier;
    }

    public function getAllSuppliersApi(): ?array
    {
        return $this->entityManager->getRepository(Supplier::class)->findAll();
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
        $this->supplierRepository->save($supplier);

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
        $supplier = $this->supplierRepository->findById($supplierMainId);

        $supplier->setSupplierId($supplierId);
        $supplier->setSupplierNr($supplierNr);
        $supplier->setSupplierName($supplierName);
        $supplier->setSupplierAddressAddition($supplierAddressAddition);
        $supplier->setSupplierAddressStreet($supplierAddressStreet);
        $supplier->setSupplierAddressStreetNr($supplierAddressStreetNr);
        $supplier->setSupplierAddressCountryCode($supplierAddressCountryCode);
        $supplier->setSupplierAddressZipcode($supplierAddressZipCode);
        $supplier->setSupplierAddressCity($supplierAddressCity);
        $this->supplierRepository->save($supplier);

        return $supplier;
    }

    public function deleteSupplierApi(int $supplierId): void
    {
        $supplier = $this->supplierRepository->findById($supplierId);

        if (!$supplier) {
            throw new NotFoundException(
                'Supplier with id '.$supplierId.' does not exist!'
            );
        } else {
            $this->supplierRepository->delete($supplier);
        }
    }

    public function getAllSuppliers(): array
    {
        $suppliers = $this->entityManager->getRepository(Supplier::class)->findAll();

        if (!$suppliers) {
            throw new NotFoundException(
                'Keine Lieferanten gefunden'
            );
        }

        return $suppliers;
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

        $numOfBoxSupplier = !empty($_GET['numOfBoxSupplier']) ? $_GET['numOfBoxSupplier'] : '';
        $nameSupp = !empty($_GET['supplier_nr']) ? strtolower(trim($_GET['supplier_nr'])) : '';

        $boxName = 'supplier_nr';

        switch ($numOfBoxSupplier) {
            case 1:
                $boxName = 'supplier_name';
                break;
            case 2:
                $boxName = 'supplier_address_addition';
                break;
            case 3:
                $boxName = 'supplier_address_street';
                break;
            case 4:
                $boxName = 'supplier_address_street_nr';
                break;
            case 5:
                $boxName = 'supplier_address_country_code';
                break;
            case 6:
                $boxName = 'supplier_address_zipcode';
                break;
            case 7:
                $boxName = 'supplier_address_city';
                break;
            case 8:
                $boxName = 'supplier_id';
                break;
        }

        $data = [];
        if (isset($_GET['name_supplier'])) {
            $nameSupp = strtolower(trim($_GET['name_supplier']));

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
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    /**
     * Get last supplier.
     */
    public function getLastSupplier(): array
    {
        return $this->entityManager->getRepository(Supplier::class)
            ->findBy([], ['supplier_nr' => 'DESC'], 1, 0);
    }
}
