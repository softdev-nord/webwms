<?php

namespace WebWMS\Repository;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getAllArticlesWithJoin()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT art.art_nr, art.art_name, art.art_kat, art.art_gew, 
                art.art_ean, art.art_einh, art.art_tiefe, art.art_breite, 
                art.art_hoehe, SUM(lbw.pos_quantity) AS lbw_menge
                FROM stock_rotation AS lbw
                RIGHT OUTER JOIN stock_location AS lpz
                    ON lbw.stock_location_id = lpz.id
                RIGHT OUTER JOIN article AS art
                    ON lbw.art_id = art.id
                GROUP BY art.id;";

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);
    }
}
