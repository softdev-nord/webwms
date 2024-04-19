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
            ->select(
                'str.id,
                 bm.movement_type,
                 bm.description,
                 str.stock_location_id,
                 CONCAT(stl.stock_location_ln, "-", stl.stock_location_fb, "-", stl.stock_location_sp, "-", stl.stock_location_tf) AS stock_location,
                 stl.stock_location_desc,
                 art.article_nr,
                 art.article_name,
                 str.pos_quantity,
                 usr.username,
                 str.access_date,
                 str.dispatch_date,
                 co.customer_order_nr,
                 so.supplier_order_nr')
            ->from('stock_rotation', 'str')
            ->leftJoin('str', 'stock_location', 'stl', 'str.stock_location_id = stl.stock_location_id')
            ->leftJoin('str', 'article', 'art', 'str.article_id = art.article_id')
            ->leftJoin('str', 'user', 'usr', 'str.usr_id = usr.id')
            ->leftJoin('str', 'booking_method', 'bm', 'bm.id = str.movement_id')
            ->leftJoin('str', 'supplier_orders', 'so', 'str.supplier_order_id = so.supplier_order_id')
            ->leftJoin('str', 'customer_orders', 'co', 'str.customer_order_id = co.customer_order_id')
            ->groupBy('str.id');

        $result = $queryBuilder->executeQuery();

        return new JsonResponse($result->fetchAllAssociative());
    }
}
