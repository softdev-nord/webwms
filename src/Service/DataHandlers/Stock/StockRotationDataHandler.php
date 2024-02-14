<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockRotationEntity;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationDataHandler
 */
class StockRotationDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function save(StockRotationEntity $stockRotationEntity): void
    {
        $this->entityManager->persist($stockRotationEntity);
        $this->entityManager->flush();
    }

    public function update(StockRotationEntity $stockRotationEntity): void
    {
        $this->entityManager->persist($stockRotationEntity);
        $this->entityManager->flush();
    }

    public function delete(StockRotationEntity $stockRotationEntity): void
    {
        $this->entityManager->remove($stockRotationEntity);
        $this->entityManager->flush();
    }

    /**
     * @return object[]
     */
    public function getAllStockRotations(): array
    {
        return $this->entityManager
            ->getRepository(StockRotationEntity::class)
            ->findAll();
    }

    /**
     * Get all Stock Rotations for Ajax-Request.
     *
     * @throws Exception
     */
    public function getAllStockRotationsWithJoin(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('str.id, bm.movement_type, bm.description, str.stock_location_id,
                    CONCAT(stl.stock_location_ln, "-", stl.stock_location_fb, "-", stl.stock_location_sp, "-", stl.stock_location_tf) AS stock_location,
                    stl.stock_location_desc, art.article_nr, art.article_name, str.pos_quantity, usr.username, str.access_date, str.dispatch_date')
            ->from('stock_rotation', 'str')
            ->innerJoin('str', 'stock_location', 'stl', 'stl.stock_location_id = str.stock_location_id')
            ->innerJoin('str', 'article', 'art', 'art.article_id = str.article_id')
            ->innerJoin('str', 'user', 'usr', 'usr.id = str.usr_id')
            ->innerJoin('str', 'booking_method', 'bm', 'bm.id = str.movement_id')
            ->groupBy('str.id');

        //        $queryBuilder
        //            ->select('*')
        //            ->from('transport_history', 'tph')
        //            ->innerJoin('tph', 'booking_method', 'bm', 'bm.movement_type = tph.booking_method')
        //            ->groupBy('stock_coordinate');

        $result = $queryBuilder->executeQuery();

        return new JsonResponse($result->fetchAllAssociative());
    }
}
