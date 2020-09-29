<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderPos;
use App\Entity\Supplier;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * Get all Orders for Ajax-Request
     * @return JsonResponse
     */
    public function getAllOrders()
    {
        $qb = $this->createQueryBuilder('bst')
            ->select('bst.id', 'bst.bst_nr', 'bst.bst_ref',
                            'sup.lief_nr', 'sup.lief_name',
                            'bst.bst_bst_dat', 'usr.username')
            ->innerJoin(OrderPos::class,
                'pos',
                'pos.bst_id = bst.id')
            ->innerJoin(Supplier::class,
                'sup',
                'sup.id = bst.lief_id')
            ->innerJoin(User::class,
                'usr',
                'bst.ben_id = usr.id')
            ->groupBy('pos.bst_id')
            ->getQuery();

        $data = $qb->getArrayResult();

        return new JsonResponse($data);
    }
}
