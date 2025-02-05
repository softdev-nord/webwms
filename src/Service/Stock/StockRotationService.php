<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Stock\StockRotationDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockRotationService'
)]
readonly class StockRotationService
{
    public function __construct(
        private StockRotationDataHandler $stockRotationDataHandler,
    ) {
    }

    /**
     * @return object[]
     */
    public function getAllStockRotations(): array
    {
        return $this->stockRotationDataHandler->getAllStockRotations();
    }

    /**
     * @throws Exception
     */
    public function getAllStockRotationsWithJoin(): JsonResponse
    {
        return $this->stockRotationDataHandler->getAllStockRotationsWithJoin();
    }
}
