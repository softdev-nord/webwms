<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\OrderPos;
use App\Entity\StockRotation;
use App\Entity\Supplier;
use App\Entity\User;
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
    public function getAllOrderPos()
    {
        $qb = $this->createQueryBuilder('pos')
            ->select('pos.bst_id', 'bst.bst_nr', 'art.art_nr',
                'art.art_name'/*, 'SUM(lbw.lbw_menge)'*/)
            ->innerJoin(Order::class,
                'bst',
                'pos.bst_id = bst.id')
            ->innerJoin(Article::class,
                'art',
                'pos.art_id = art.id')
            /*->leftJoin(StockRotation::class,
                'lbw',
                'pos.id = lbw.bst_pos_id')*/
            ->groupBy('pos.id')
            ->orderBy('pos.id')
            ->getQuery();

        $data = $qb->getArrayResult();

        return new JsonResponse($data);
    }
}
