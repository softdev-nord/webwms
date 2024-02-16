<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\SupplierOrderPos;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrderPosEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\SupplierOrderPosEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosDataHandler
 */
class SupplierOrderPosDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $this->entityManager->persist($supplierOrderPosEntity);
        $this->entityManager->flush();
    }

    public function delete(SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $this->entityManager->remove($supplierOrderPosEntity);
        $this->entityManager->flush();
    }

    public function getSupplierOrderPosById(int $supplierOrderPosId): ?SupplierOrderPosEntity
    {
        return $this->entityManager
            ->getRepository(SupplierOrderPosEntity::class)
            ->findOneBy(['id' => $supplierOrderPosId]);
    }

    public function getSupplierOrderPosBySupplierOrderId(int $supplierOrderId): ?SupplierOrderPosEntity
    {
        return $this->entityManager
            ->getRepository(SupplierOrderPosEntity::class)
            ->find($supplierOrderId);
    }

    public function getAllSupplierOrderPos(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();

        $sql = "SELECT pos.supplier_order_id, ord.supplier_order_nr, art.article_nr, art.article_name, pos.supplier_order_pos_quantity,
                (SELECT (SUM(IF(transport_history.tr_type = '1', transport_history.tr_quantity, 0.000))) FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM supplier_order_pos AS pos
                INNER JOIN supplier_orders AS ord
                    ON pos.supplier_order_id = ord.supplier_order_id
                INNER JOIN article AS art
                    ON pos.article_id = art.article_id
                LEFT OUTER JOIN transport_history AS lbw
                    ON ord.supplier_order_nr = lbw.order_nr
                GROUP BY pos.article_id ORDER BY pos.article_id";

        $data = $connection->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    public function addSupplierOrderPos(SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $supplierOrderPosEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($supplierOrderPosEntity);
    }

    public function updateSupplierOrderPos(SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        $supplierOrderPosEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($supplierOrderPosEntity);
    }

    public function deleteSupplierOrderPos(?SupplierOrderPosEntity $supplierOrderPosEntity): void
    {
        if ($supplierOrderPosEntity instanceof SupplierOrderPosEntity) {
            $this->delete($supplierOrderPosEntity);
        }
    }
}
