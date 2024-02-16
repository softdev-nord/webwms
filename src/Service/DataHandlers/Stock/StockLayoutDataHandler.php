<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLayoutEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutDataHandler
 */
class StockLayoutDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(StockLayoutEntity $stockLayoutEntity): void
    {
        $this->entityManager->persist($stockLayoutEntity);
        $this->entityManager->flush();
    }

    public function delete(StockLayoutEntity $stockLayoutEntity): void
    {
        $this->entityManager->remove($stockLayoutEntity);
        $this->entityManager->flush();
    }

    public function getAllStockLayouts(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('stock_layout');

        $result = $queryBuilder->executeQuery();
        $results = $result->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getStockLayoutById(int $stockLayoutId): ?StockLayoutEntity
    {
        return $this->entityManager
            ->getRepository(StockLayoutEntity::class)
            ->findOneBy(['id' => $stockLayoutId]);
    }

    public function addStockLayout(StockLayoutEntity $stockLayoutEntity): void
    {
        $stockLayoutEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLayoutEntity);
    }

    public function updateStockLayout(StockLayoutEntity $stockLayoutEntity): void
    {
        $stockLayoutEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLayoutEntity);
    }

    public function deleteStockLayout(StockLayoutEntity $stockLayoutEntity): void
    {
        $this->delete($stockLayoutEntity);
    }
}
