<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLayout;
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
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function save(StockLayout $stockLayout): void
    {
        $this->entityManager->persist($stockLayout);
        $this->entityManager->flush();
    }

    public function delete(StockLayout $stockLayout): void
    {
        $this->entityManager->remove($stockLayout);
        $this->entityManager->flush();
    }

    public function getAllStockLayouts(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('stock_layout');

        $stmt = $queryBuilder->executeQuery();
        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }

    public function getStockLayoutById(int $stockLayoutId): ?StockLayout
    {
        return $this->entityManager
            ->getRepository(StockLayout::class)
            ->findOneBy(['id' => $stockLayoutId]);
    }

    public function addStockLayout(StockLayout $stockLayout): void
    {
        $stockLayout->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLayout);
    }

    public function updateStockLayout(StockLayout $stockLayout): void
    {
        $stockLayout->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($stockLayout);
    }

    public function deleteStockLayout(StockLayout $stockLayout): void
    {
        $this->delete($stockLayout);
    }
}
