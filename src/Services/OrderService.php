<?php

declare(strict_types=1);

namespace WebWMS\Services;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\Order as Orders;

/**
 * @package:    WebWMS\Services
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        OrderService
 */
class OrderService
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
     * Get all Orders for Ajax-Request.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getAllOrders(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select('ord.order_id, ord.order_nr, ord.order_reference,
            sup.supplier_nr, sup.supplier_name, ord.order_order_date, usr.username')
            ->from('orders', 'ord')
            ->innerJoin('ord', 'order_pos', 'op', 'op.order_id = ord.order_id')
            ->innerJoin('ord', 'supplier', 'sup', 'ord.supplier_id = sup.id')
            ->innerJoin('ord', 'user', 'usr', 'ord.usr_id = usr.id')
            ->groupBy('op.order_id');

        $stmt = $queryBuilder->execute();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    public function getAllOrderPos(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $sql = "SELECT pos.order_id, ord.order_nr, art.article_nr, art.article_name, pos.order_pos_quantity,
                (SELECT (SUM(IF(transport_history.tr_typ = '1', transport_history.tr_quantity, 0.000))) FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM order_pos AS pos
                INNER JOIN orders AS ord
                    ON pos.order_id = ord.order_id
                INNER JOIN article AS art
                    ON pos.article_id = art.id
                LEFT OUTER JOIN transport_history AS lbw
                    ON ord.order_nr = lbw.order_nr
                GROUP BY pos.article_id ORDER BY pos.article_id";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    /**
     * Get last customer order id.
     *
     * @return object[]
     */
    public function getLastOrderId(): array
    {
        $customerOrderRepository = $this->doctrine->getRepository(Orders::class);

        return $customerOrderRepository->findBy([], ['order_id' => 'DESC'], 1, 0);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}