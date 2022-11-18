<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\SupplierOrder;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\SupplierOrder;

/**
 * @package:    WebWMS\Service\DataHandlers\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderDataHandler
 */
class SupplierOrderDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(SupplierOrder $supplierOrder): void
    {
        $this->entityManager->persist($supplierOrder);
        $this->entityManager->flush();
    }

    public function update(SupplierOrder $supplierOrder): void
    {
        $this->entityManager->persist($supplierOrder);
        $this->entityManager->flush();
    }

    public function delete(SupplierOrder $supplierOrder): void
    {
        $this->entityManager->remove($supplierOrder);
        $this->entityManager->flush();
    }

    /**
     * @return SupplierOrder|null Returns an array of Customer order objects
     */
    public function getSupplierOrderById(int $supplierOrderId): ?SupplierOrder
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->find($supplierOrderId);
    }

    public function updateSupplierOrder($requestData): ?SupplierOrder
    {
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $supplierOrder = $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findOneBy(['supplier_order_nr' => $requestData['supplier_order_nr']]);

        if (!$supplierOrder) {
            return null;
        }

        $supplierOrder->setSupplierOrderId((int) $requestData['	supplier_order_id']);
        $supplierOrder->setUsrId((int) $requestData['usr_id']);
        $supplierOrder->setSupplierId((int) $requestData['supplier_id']);
        $supplierOrder->setSupplierOrderNr((string)$requestData['supplier_order_nr']);
        $supplierOrder->setSupplierOrderReference((string) $requestData['supplier_order_reference']);
        $supplierOrder->setSupplierOrderDate($requestData['supplier_order_date']);
        $supplierOrder->setSupplierOrderCreationDate($requestData['supplier_address_street']);
        $supplierOrder->setUpdatedAt($updatedAt);

        $this->update($supplierOrder);

        return $supplierOrder;
    }
}
