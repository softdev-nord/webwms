<?php

namespace WebWMS\Repository;

use WebWMS\Entity\Article;
use WebWMS\Entity\Order;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Entity\StockRotation;
use WebWMS\Entity\Supplier;
use WebWMS\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method CustomerOrderPos|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerOrderPos|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerOrderPos[]    findAll()
 * @method CustomerOrderPos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerOrderPosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerOrderPos::class);
    }

    /**
     * Get all Customer Order Pos for Ajax-Request
     * @return JsonResponse
     */
    public function getAllCustomerOrderPos()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT cop.customer_order_id, co.customer_order_nr, art.art_nr,
                    art.art_name, cop.customer_order_pos_quantity, lbw.pos_quantity AS lbw_menge
                FROM customer_order_pos AS cop
                INNER JOIN customer_orders AS co
                    ON cop.customer_order_id = co.customer_order_id
                INNER JOIN article AS art
                    ON cop.article_id = art.id
                LEFT OUTER JOIN stock_rotation lbw
                    ON cop.id = lbw.customer_order_id
                ORDER BY cop.id;";

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);

    }
}
