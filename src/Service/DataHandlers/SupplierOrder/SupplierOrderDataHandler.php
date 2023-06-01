<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\SupplierOrder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
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

    public function getSupplierOrderByNr(string $supplierOrderNr): ?SupplierOrder
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findOneBy(['supplierOrderNr' => $supplierOrderNr]);
    }

    public function getAllSupplierOrder(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select(
                'so.supplier_order_id',
                'so.supplier_order_nr',
                'so.supplier_order_reference',
                'sup.supplier_nr',
                'sup.supplier_name',
                'so.supplier_order_creation_date',
                'usr.username',
                'so.created_at, so.updated_at'
            )
            ->from('supplier_orders', 'so')
            ->innerJoin('so', 'supplier_order_pos', 'sop', 'sop.supplier_order_id = so.supplier_order_id')
            ->innerJoin('so', 'supplier', 'sup', 'so.supplier_id = sup.supplier_id')
            ->innerJoin('so', 'user', 'usr', 'so.usr_id = usr.id')
            ->groupBy('sop.supplier_order_id');

        $stmt = $queryBuilder->executeQuery();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
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

    /**
     * @return array<int, SupplierOrder>
     */
    public function getLastSupplierOrderId(): array
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findBy([], ['supplierOrderId' => 'DESC'], 1, 0);
    }
}
