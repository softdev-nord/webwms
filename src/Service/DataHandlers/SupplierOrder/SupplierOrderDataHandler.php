<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\SupplierOrder;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderDataHandler
 */
class SupplierOrderDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(SupplierOrderEntity $supplierOrderEntity): void
    {
        $this->entityManager->persist($supplierOrderEntity);
        $this->entityManager->flush();
    }

    public function delete(SupplierOrderEntity $supplierOrderEntity): void
    {
        $this->entityManager->remove($supplierOrderEntity);
        $this->entityManager->flush();
    }

    public function getSupplierOrderById(int $supplierOrderId): ?SupplierOrderEntity
    {
        return $this->entityManager
            ->getRepository(SupplierOrderEntity::class)
            ->findOneBy(['supplierOrderId' => $supplierOrderId]);
    }

    public function getSupplierOrderByNr(string $supplierOrderNr): ?SupplierOrderEntity
    {
        return $this->entityManager
            ->getRepository(SupplierOrderEntity::class)
            ->findOneBy(['supplierOrderNr' => $supplierOrderNr]);
    }

    public function getAllSupplierOrder(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();

        $queryBuilder = $connection->createQueryBuilder();

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

    public function addSupplierOrder(SupplierOrderEntity $supplierOrderEntity): void
    {
        $newSupplierOrder = (new SupplierOrderEntity())
            ->setSupplierOrderId($supplierOrderEntity->getSupplierOrderId())
            ->setUsrId($supplierOrderEntity->getUsrId())
            ->setSupplierId($supplierOrderEntity->getSupplierId())
            ->setSupplierOrderNr($supplierOrderEntity->getSupplierOrderNr())
            ->setSupplierOrderReference($supplierOrderEntity->getSupplierOrderReference())
            ->setSupplierOrderDate($supplierOrderEntity->getSupplierOrderDate())
            ->setSupplierOrderCreationDate($supplierOrderEntity->getSupplierOrderCreationDate())
            ->setCreatedAt($this->dateTimeService->createDateTime())
        ;

        $this->save($newSupplierOrder);
    }

    public function updateSupplierOrder(SupplierOrderEntity $supplierOrderEntity): void
    {
        $supplierOrderEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($supplierOrderEntity);
    }

    public function deleteSupplierOrder(SupplierOrderEntity $supplierOrderEntity): void
    {
        $this->delete($supplierOrderEntity);
    }

    /**
     * @return array<int, SupplierOrderEntity>
     */
    public function getLastSupplierOrderId(): array
    {
        return $this->entityManager
            ->getRepository(SupplierOrderEntity::class)
            ->findBy([], ['supplierOrderId' => 'DESC'], 1, 0);
    }
}
