<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\StockLayout;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLayoutService
 */
class StockLayoutService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getStockLayout(): array
    {
        return $this->entityManager
            ->getRepository(StockLayout::class)->findAll();
    }
}