<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZone;
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
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(StockZone $stockZone): void
    {
        $this->entityManager->persist($stockZone);
        $this->entityManager->flush();
    }

    public function delete(StockZone $stockZone): void
    {
        $this->entityManager->remove($stockZone);
        $this->entityManager->flush();
    }

    public function getAllStockZones(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('stock_zone');

        $stmt = $queryBuilder->executeQuery();
        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getStockZoneById(int $stockZoneId): ?StockZone
    {
        return $this->entityManager
            ->getRepository(StockZone::class)
            ->findOneBy(['id' => $stockZoneId]);
    }

    public function addStockZone(StockZone $stockZone): void
    {
        $stockZone->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($stockZone);
    }

    public function updateStockZone(StockZone $stockZone): void
    {
        $stockZone->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockZone);
    }

    public function deleteStockZone(StockZone $stockZone): void
    {
        $this->delete($stockZone);
    }
}
