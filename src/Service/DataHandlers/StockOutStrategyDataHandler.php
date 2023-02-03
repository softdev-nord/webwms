<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers;

use Doctrine\ORM\EntityManagerInterface;

class StockOutStrategyDataHandler
{
    public function __construct(
        // private EntityManagerInterface $entityManager
    ) {
    }

    public function fiFoStrategy(): void
    {
        // TODO: Implement logic
    }

    public function feFoStrategy(): void
    {
        // TODO: Implement logic
    }

    public function liFoStrategy(): void
    {
        // TODO: Implement logic
    }

    public function hiFoStrategy(): void
    {
        // TODO: Implement logic
    }

    public function loFoStrategy(): void
    {
        // TODO: Implement logic
    }

    public function chaoticStorageStockOutStrategy(): void
    {
        // TODO: Implement logic
    }
}
