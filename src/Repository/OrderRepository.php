<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Order;
use WebWMS\Entity\OrderPos;
use WebWMS\Entity\Supplier;
use WebWMS\Entity\User;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Get all Orders for Ajax-Request.
     *
     * @return JsonResponse
     */
    /*public function getAllOrders()
    {
        $qb = $this->createQueryBuilder('bst')
            ->select('bst.bst_id', 'bst.bst_nr', 'bst.bst_ref',
                            'sup.lief_nr', 'sup.lief_name',
                            'bst.bst_bst_dat', 'usr.username')
            ->innerJoin(OrderPos::class,
                'pos',
                'pos.bst_id = bst.bst_id')
            ->innerJoin(Supplier::class,
                'sup',
                'sup.id = bst.lief_id')
            ->innerJoin(User::class,
                'usr',
                'bst.ben_id = usr.id')
            //->groupBy('bst.bst_id')
            ->getQuery();

        $data = $qb->getArrayResult();

        return new JsonResponse($data);
    }*/

    /**
     * Get all Orders for Ajax-Request.
     *
     * @return JsonResponse
     */
    public function getAllOrders()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT bst.bst_id, bst.bst_nr, bst.bst_ref, lief.lief_nr, lief.lief_name, 
				bst.bst_bst_dat, usr.username
				FROM orders AS bst
					INNER JOIN order_pos AS pos
				ON pos.bst_id = bst.bst_id
					INNER JOIN supplier AS lief
				ON lief.id = bst.lief_id
					INNER JOIN user AS usr 
				ON bst.ben_id = usr.id
				GROUP BY pos.bst_id';

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);
    }
}
