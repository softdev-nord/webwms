<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Stock;

use Doctrine\ORM\EntityManagerInterface;
use WebWMS\Entity\StockLayout;

/**
 * @package:    WebWMS\Service\DataHandlers\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLayoutDataHandler
 */
class StockLayoutDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return object[]
     */
    public function getStockLayout(): array
    {
        return $this->entityManager
            ->getRepository(StockLayout::class)->findAll();
    }
}
