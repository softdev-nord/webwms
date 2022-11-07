<?php

declare(strict_types=1);

namespace WebWMS\Service\SupplierOrder;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Exception\NotFoundException;
use WebWMS\Service\DataHandlers\SupplierOrder\SupplierOrderDataHandler;

/**
 * @package:    WebWMS\Service
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

    /**
     * @throws NotFoundException
     */
    public function getSupplierOrderApi(int $supplierOrderId): ?SupplierOrder
    {
        $order = $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->find($supplierOrderId);

        if (!$order) {
            throw new NotFoundException('Supplier order with id '.$supplierOrderId.' does not exist!');
        }

        return $order;
    }

    /**
     * @throws \Exception
     */
    public function getAllSupplierOrdersApi(): array
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findBy([], ['supplierOrderId' => 'ASC']);
    }

    public function addSupplierOrderApi(
        int $supplierOrderId,
        int $usrId,
        int $supplierId,
        string $supplierOrderNr,
        string $supplierOrderReference,
        $supplierOrderDate,
        $supplierOrderOrderDate
    ): SupplierOrder {
        $supplierOrder = new SupplierOrder();
        $supplierOrder->setSupplierOrderId($supplierOrderId);
        $supplierOrder->setUsrId($usrId);
        $supplierOrder->setSupplierId($supplierId);
        $supplierOrder->setSupplierOrderNr($supplierOrderNr);
        $supplierOrder->setSupplierOrderReference($supplierOrderReference);
        $supplierOrder->setSupplierOrderDate($supplierOrderDate);
        $supplierOrder->setSupplierOrderOrderDate($supplierOrderOrderDate);
        $this->supplierOrderDataHandler->save($supplierOrder);

        return $supplierOrder;
    }

    public function updateSupplierOrderApi(
        int $supplierOrderMainId,
        int $supplierOrderId,
        int $usrId,
        int $supplierId,
        string $supplierOrderNr,
        string $supplierOrderReference,
        $supplierOrderDate,
        $supplierOrderOrderDate
    ): ?SupplierOrder {
        $supplierOrder = $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->find($supplierOrderMainId);

        $supplierOrder->setSupplierOrderId($supplierOrderId);
        $supplierOrder->setUsrId($usrId);
        $supplierOrder->setSupplierId($supplierId);
        $supplierOrder->setSupplierOrderNr($supplierOrderNr);
        $supplierOrder->setSupplierOrderReference($supplierOrderReference);
        $supplierOrder->setSupplierOrderDate($supplierOrderDate);
        $supplierOrder->setSupplierOrderOrderDate($supplierOrderOrderDate);

        return $supplierOrder;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function deleteSupplierOrderApi(int $supplierOrderId): void
    {
        $supplierOrder = $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->find($supplierOrderId);

        if (!$supplierOrder) {
            throw new NotFoundException('Supplier order with id '.$supplierOrderId.' does not exist!');
        } else {
            $this->supplierOrderDataHandler->delete($supplierOrder);
        }
    }

    /**
     * Get all Orders for Ajax-Request.
     *
     * @throws Exception
     */
    public function getAllOrders(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select('so.supplier_order_id, so.supplier_order_nr, so.supplier_order_reference,
            sup.supplier_nr, sup.supplier_name, so.supplier_order_order_date, usr.username')
            ->from('supplier_orders', 'so')
            ->innerJoin('so', 'supplier_order_pos', 'sop', 'sop.supplier_order_id = so.supplier_order_id')
            ->innerJoin('so', 'supplier', 'sup', 'so.supplier_id = sup.id')
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
                (SELECT (SUM(IF(transport_history.tr_typ = '1', transport_history.tr_quantity, 0.000))) FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
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

    /**
     * Get last customer order id.
     *
     * @return object[]
     */
    public function getLastSupplierOrderId(): array
    {
        return $this->entityManager
            ->getRepository(SupplierOrder::class)
            ->findBy([], ['supplierOrderId' => 'DESC'], 1, 0);
    }
}
