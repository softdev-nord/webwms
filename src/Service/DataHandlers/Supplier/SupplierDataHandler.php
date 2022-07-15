<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Supplier;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Service\DataHandlers\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierDataHandler
 */
class SupplierDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(Supplier $supplier): void
    {
        $this->entityManager->persist($supplier);
        $this->entityManager->flush();
    }

    public function update(Supplier $supplier): void
    {
        $this->entityManager->persist($supplier);
        $this->entityManager->flush();
    }

    public function delete(Supplier $supplier): void
    {
        $this->entityManager->remove($supplier);
        $this->entityManager->flush();
    }

    public function updateSupplier($requestData): ?Supplier
    {
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $supplier = $this->entityManager
            ->getRepository(Supplier::class)
            ->findOneBy(['supplier_nr' => $requestData['supplier_nr']]);

        if (!$supplier) {
            return null;
        }

        $supplier->setSupplierId((int) $requestData['supplier_id']);
        $supplier->setSupplierNr((int) $requestData['supplier_nr']);
        $supplier->setSupplierName((string) $requestData['supplier_name']);
        $supplier->setSupplierAddressAddition((int) $requestData['supplier_address_addition']);
        $supplier->setSupplierAddressStreet((string) $requestData['supplier_address_street']);
        $supplier->setSupplierAddressStreetNr((string) $requestData['supplier_address_street_nr']);
        $supplier->setSupplierAddressCountryCode((string) $requestData['supplier_address_country_code']);
        $supplier->setSupplierAddressZipcode((string) $requestData['supplier_address_zipcode']);
        $supplier->setSupplierAddressCity((string) $requestData['supplier_address_city']);
        $supplier->setSupplierUpdatedAt($updatedAt);

        $this->update($supplier);

        return $supplier;
    }
}
