<?php

declare(strict_types=1);

namespace WebWMS\Services;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\CustomerOrder as CustomerOrders;

class CustomerOrderService
{
    /** @var ManagerRegistry */
    private $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
     * Get all Customer Orders for Ajax-Request.
     */
    public function getAllCustomerOrders(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        $queryBuilder
            ->select('co.customer_order_id', 'co.customer_order_nr', 'co.customer_order_reference',
            'cu.customer_nr', 'cu.customer_name', 'co.customer_order_date',
            'co.customer_order_order_date', 'usr.username')
            ->from('customer_orders', 'co')
            ->innerJoin('co', 'customer_order_pos', 'cop', 'cop.customer_order_id = co.customer_order_id')
            ->innerJoin('co', 'customer', 'cu', 'cu.customer_id = co.customer_id')
            ->innerJoin('co', 'user', 'usr', 'usr.id = co.usr_id')
            ->groupBy('cop.customer_order_id');

        $stmt = $queryBuilder->execute();

        $result = $stmt->fetchAllAssociative();

        return new JsonResponse($result);
    }

    /**
     * Get all Customer Order Pos for Ajax-Request.
     * @throws Exception
     */
    public function getAllCustomerOrderPos(): JsonResponse
    {
        $conn = $this->doctrine->getConnection();

        $sql = "SELECT DISTINCT cop.customer_order_id, th.order_nr, art.article_nr, art.article_name, cop.customer_order_pos_quantity, th.tr_quantity AS lbw_menge
FROM transport_history AS th
         INNER JOIN article AS art
                    ON th.article_nr = art.article_nr
         INNER JOIN customer_orders AS co
                    ON th.doc_id = co.customer_order_id
         LEFT OUTER JOIN customer_order_pos AS cop
                         ON th.order_nr LIKE CONCAT('%', cop.customer_order_id ,'%')";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    /**
     * Get last customer order id.
     *
     * @return object[]
     */
    public function getLastCustomerOrderId(): array
    {
        $customerOrderRepository = $this->doctrine
            ->getRepository(CustomerOrders::class);

        return $customerOrderRepository->findBy([], ['customer_order_id' => 'DESC'], 1, 0);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}