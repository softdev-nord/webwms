<?php

namespace WebWMS\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;

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

        $sql = 'SELECT art.art_nr, art.art_name, art.art_kat, art.art_gew, 
                art.art_ean, art.art_einh, art.art_tiefe, art.art_breite, 
                art.art_hoehe, SUM(lbw.pos_quantity) AS lbw_menge
                FROM stock_rotation AS lbw
                RIGHT OUTER JOIN stock_location AS lpz
                    ON lbw.stock_location_id = lpz.id
                RIGHT OUTER JOIN article AS art
                    ON lbw.art_id = art.id
                GROUP BY art.id;';

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getArticle()
    {
        $connection = $this->getEntityManager()->getConnection();

        $numOfBoxArt = !empty($_GET['numOfBoxArt']) ? $_GET['numOfBoxArt'] : '';
        $name = !empty($_GET['art_nr']) ? strtolower(trim($_GET['art_nr'])) : '';

        $boxName = 'art_nr';

        switch ($numOfBoxArt) {
            case 1:
                $boxName = 'art_name';
                break;
            case 2:
                $boxName = 'id';
                break;
            case 3:
                $boxName = 'art_ean';
                break;
            case 4:
                $boxName = 'art_kat';
                break;
        }

        $data = [];
        if (!empty($_GET['name_art'])) {
            $name = strtolower(trim($_GET['name_art']));

            $sqlArt = "SELECT art_nr, art_name, id, art_ean, art_kat FROM article where LOWER($boxName) LIKE '".$name."%'";
            $stmt = $connection->query($sqlArt);

            while ($row = $stmt->fetch()) {
                $name = $row['art_nr'].'|'.$row['art_name'].'|'.$row['id'].'|'.$row['art_ean'].'|'.$row['art_kat'];
                array_push($data, $name);
            }
        }

        return new JsonResponse($data);
    }
}
