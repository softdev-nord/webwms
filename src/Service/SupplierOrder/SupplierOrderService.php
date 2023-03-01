<?php

declare(strict_types=1);

namespace WebWMS\Service\SupplierOrder;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Service\DataHandlers\SupplierOrder\SupplierOrderDataHandler;

/**
 * @package:    WebWMS\Service\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderService
 */
class SupplierOrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SupplierOrderDataHandler $supplierOrderDataHandler
    ) {
    }

    public function getSupplierOrderById(int $supplierId): ?SupplierOrder
    {
        return $this->supplierOrderDataHandler->getSupplierOrderById($supplierId);
    }

    public function getSupplierOrderByNr(int $supplierNr): ?SupplierOrder
    {
        return $this->supplierOrderDataHandler->getSupplierOrderById($supplierNr);
    }

    public function getAllSupplierOrder(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select('so.supplier_order_id, so.supplier_order_nr, so.supplier_order_reference,
            sup.supplier_nr, sup.supplier_name, so.supplier_order_creation_date, usr.username, so.created_at, so.updated_at')
            ->from('supplier_orders', 'so')
            ->innerJoin('so', 'supplier_order_pos', 'sop', 'sop.supplier_order_id = so.supplier_order_id')
            ->innerJoin('so', 'supplier', 'sup', 'so.supplier_id = sup.supplier_id')
            ->innerJoin('so', 'user', 'usr', 'so.usr_id = usr.id')
            ->groupBy('sop.supplier_order_id');

        $stmt = $queryBuilder->executeQuery();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    /**
     * @throws Exception
     */
    public function getAllOrderPos(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

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

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    public function addSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->supplierOrderDataHandler->addSupplierOrder($supplierOrder);
    }

    public function updateSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->supplierOrderDataHandler->updateSupplierOrder($supplierOrder);
    }

    public function deleteSupplierOrder(SupplierOrder $supplierOrder): void
    {
        $this->supplierOrderDataHandler->deleteSupplierOrder($supplierOrder);
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
