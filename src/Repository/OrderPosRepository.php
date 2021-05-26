<?php

namespace WebWMS\Repository;

use WebWMS\Entity\Article;
use WebWMS\Entity\Order;
use WebWMS\Entity\OrderPos;
use WebWMS\Entity\StockRotation;
use WebWMS\Entity\Supplier;
use WebWMS\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method OrderPos|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderPos|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderPos[]    findAll()
 * @method OrderPos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderPosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderPos::class);
    }

    /**
     * Get all Order positions for Ajax-Request
     * @return JsonResponse
     */
    /*public function getAllOrderPos()
    {
        $qb = $this->createQueryBuilder('pos')
            ->select('pos.bst_id', 'bst.bst_nr', 'art.art_nr',
                'art.art_name', 'pos.bst_pos_menge', 'SUM(lbw.lbw_menge) as lbw_menge')
            ->innerJoin(Order::class,
                'bst',
                'pos.bst_id = bst.bst.id')
            ->innerJoin(Article::class,
                'art',
                'pos.art_id = art.id')
            ->innerJoin(StockRotation::class,
                'lbw',
                'pos.id = lbw.bst_pos_id')
            ->groupBy('lbw.bst_pos_id')
            ->orderBy('lbw.bst_pos_id')
            ->getQuery();

        $data = $qb->getArrayResult();

        return new JsonResponse($data);
    }*/

    /**
     * @return JsonResponse
     */
    public function getAllOrderPos()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT pos.bst_id, bst.bst_nr, art.art_nr, art.art_name, pos.bst_pos_menge, SUM(lbw.pos_quantity) AS lbw_menge
				FROM order_pos AS pos
					INNER JOIN orders AS bst
				ON pos.bst_id = bst.bst_id
					INNER JOIN article AS art 
				ON pos.art_id = art.id
					LEFT OUTER JOIN stock_rotation AS lbw 
				ON pos.id = lbw.customer_order_id
				GROUP BY pos.id ORDER BY pos.id;";

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);

    }
}
