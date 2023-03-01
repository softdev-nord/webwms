<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\SupplierOrder;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderDataHandler
 */
class SupplierOrderDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(SupplierOrder $supplierOrder): void
    {
        $this->entityManager->persist($supplierOrder);
        $this->entityManager->flush();
    }

    public function delete(SupplierOrder $supplierOrder): void
    {
        $this->entityManager->remove($supplierOrder);
        $this->entityManager->flush();
    }

    public function getSupplierOrderById(int $supplierOrderId): ?SupplierOrder
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findOneBy(['supplierOrderId' => $supplierOrderId]);
    }

    public function getSupplierOrderByNr(int $supplierOrderNr): ?SupplierOrder
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findOneBy(['supplierOrderNr' => $supplierOrderNr]);
    }

    public function addSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $newSupplierOrder = new SupplierOrder();

        $newSupplierOrder->setSupplierOrderId($supplierOrder->getSupplierOrderId());
        $newSupplierOrder->setUsrId($supplierOrder->getUsrId());
        $newSupplierOrder->setSupplierId($supplierOrder->getSupplierId());
        $newSupplierOrder->setSupplierOrderNr($supplierOrder->getSupplierOrderNr());
        $newSupplierOrder->setSupplierOrderReference($supplierOrder->getSupplierOrderReference());
        $newSupplierOrder->setSupplierOrderDate($supplierOrder->getSupplierOrderDate());
        $newSupplierOrder->setSupplierOrderCreationDate($supplierOrder->getSupplierOrderCreationDate());
        $newSupplierOrder->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($newSupplierOrder);
    }

    public function updateSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $supplierOrder->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($supplierOrder);
    }

    public function deleteSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->delete($supplierOrder);
    }
}
