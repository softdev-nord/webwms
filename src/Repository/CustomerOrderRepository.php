<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\CustomerOrder;

/**
 * @method CustomerOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerOrder[]    findAll()
 * @method CustomerOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerOrder::class);
    }

    /**
     * Get all Customer Orders for Ajax-Request.
     *
     * @return JsonResponse
     */
    public function getAllCustomerOrders()
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = $conn->createQueryBuilder();

        /*$queryBuilder->select(['co.customer_order_id', 'co.customer_order_nr', 'co.customer_order_reference',
            'cu.customer_nr', 'cu.customer_name', 'co.customer_order_date',
            'co.customer_order_order_date', 'usr.username'])
            ->from('customer_orders', 'co')
            ->innerJoin('co', 'customer_order_pos', 'co_pos', 'co_pos.customer_order_id = co.customer_order_id')
            ->innerJoin('co', 'customer', 'cu', 'cu.customer_id = co.customer_id')
            ->innerJoin('co', 'user', 'usr', 'usr.id = co.usr_id')
            ->groupBy('co_pos.customer_order_id');

        $stmt = $queryBuilder->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);*/

        $sql = 'SELECT co.customer_order_id, co.customer_order_nr, co.customer_order_reference,
                     cu.customer_nr, cu.customer_name, co.customer_order_date,
                     co.customer_order_order_date, usr.username
                 FROM customer_orders AS co
                 INNER JOIN customer_order_pos AS co_pos
                     ON co_pos.customer_order_id = co.customer_order_id
                 INNER JOIN customer AS cu
                     ON cu.customer_id = co.customer_id
                 INNER JOIN user AS usr
                     ON usr.id = co.usr_id
                 GROUP BY co_pos.customer_order_id;';

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);
    }
}
