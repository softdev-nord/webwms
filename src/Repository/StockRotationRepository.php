<?php

namespace WebWMS\Repository;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockRotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StockRotation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockRotation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockRotation[]    findAll()
 * @method StockRotation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockRotation::class);
    }

    /**
     * Get all Customer Orders for Ajax-Request
     * @return JsonResponse
     */
    public function getAllStockRotationsWithJoin()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT str.id, bm.bm_short, bm.bm_desc,
                    CONCAT(stl.stock_location_ln, '-', stl.stock_location_fb, '-', stl.stock_location_sp, '-', stl.stock_location_tf) AS stock_location,
                    stl.stock_location_desc, art.art_nr, art.art_name, str.pos_quantity, usr.username, str.access_date, str.dispatch_date
                FROM stock_rotation AS str
                INNER JOIN stock_location AS stl
                    ON stl.id = str.stock_location_id
                INNER JOIN article AS art
                    ON art.id = str.art_id
                INNER JOIN user AS usr
                    ON usr.id = str.usr_id
                INNER JOIN booking_method AS bm
                    ON bm.id = str.movement_id
                GROUP BY str.id;";

        $data = $conn->fetchAll($sql);

        return new JsonResponse($data);
    }
}
