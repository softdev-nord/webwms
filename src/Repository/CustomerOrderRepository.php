<?php

namespace WebWMS\Repository;

use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Entity\Supplier;
use WebWMS\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * Get all Customer Orders for Ajax-Request
     * @return JsonResponse
     */
    public function getAllCustomerOrders()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT aft.customer_order_id, aft.customer_order_nr, aft.customer_order_reference,
                    kd.customer_nr, kd.customer_name, aft.customer_order_date,
                    aft.customer_order_order_date, usr.username
                FROM customer_orders AS aft
                INNER JOIN customer_order_pos AS pos
                    ON pos.customer_order_id = aft.customer_order_id
                INNER JOIN customer AS kd
                    ON kd.customer_id = aft.customer_id
                INNER JOIN user AS usr
                    ON usr.id = aft.usr_id
                GROUP BY pos.customer_order_id;";

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);
    }
}
