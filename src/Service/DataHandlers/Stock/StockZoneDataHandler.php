<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneDataHandler
 */
class StockZoneDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(StockZoneEntity $stockZoneEntity): void
    {
        $this->entityManager->persist($stockZoneEntity);
        $this->entityManager->flush();
    }

    public function delete(StockZoneEntity $stockZoneEntity): void
    {
        $this->entityManager->remove($stockZoneEntity);
        $this->entityManager->flush();
    }

    public function getAllStockZones(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('stock_zone');

        $result = $queryBuilder->executeQuery();
        $results = $result->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getStockZoneById(int $stockZoneId): ?StockZoneEntity
    {
        return $this->entityManager
            ->getRepository(StockZoneEntity::class)
            ->findOneBy(['id' => $stockZoneId]);
    }

    public function addStockZone(StockZoneEntity $stockZoneEntity): void
    {
        $stockZoneEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($stockZoneEntity);
    }

    public function updateStockZone(StockZoneEntity $stockZoneEntity): void
    {
        $stockZoneEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockZoneEntity);
    }

    public function deleteStockZone(StockZoneEntity $stockZoneEntity): void
    {
        $this->delete($stockZoneEntity);
    }
}
