<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers;

use Doctrine\ORM\EntityManagerInterface;

class StockOutStrategyDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function fiFoStrategy()
    {
        // TODO: Implement logic
    }

    public function feFoStrategy()
    {
        // TODO: Implement logic
    }

    public function liFoStrategy()
    {
        // TODO: Implement logic
    }

    public function hiFoStrategy()
    {
        // TODO: Implement logic
    }

    public function loFoStrategy()
    {
        // TODO: Implement logic
    }

    public function chaoticStorageStockOutStrategy()
    {
        // TODO: Implement logic
    }
}
