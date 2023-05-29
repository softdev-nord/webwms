<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\DataHandlers\StockOutStrategyDataHandler;
use WebWMS\Service\Stock\StockOutStrategyService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOutStrategyServiceTest
 *
 * @covers \WebWMS\Service\Stock\StockOutStrategyService
 */
final class StockOutStrategyServiceTest extends TestCase
{
    private StockOutStrategyService $stockOutStrategyService;

    /**
     * @var (StockOutStrategyDataHandler&MockObject)|MockObject
     */
    private MockObject|StockOutStrategyDataHandler $stockOutStrategyDataHandler;

    protected function setUp(): void
    {
        $this->stockOutStrategyDataHandler = $this->createMock(StockOutStrategyDataHandler::class);
        $this->stockOutStrategyService = new StockOutStrategyService($this->stockOutStrategyDataHandler);
    }

    public function testFiFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler
            ->expects(self::once())
            ->method('fiFoStrategy');

        $this->stockOutStrategyService->fiFoStrategy();
    }

    public function testFeFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler
            ->expects(self::once())
            ->method('feFoStrategy');

        $this->stockOutStrategyService->feFoStrategy();
    }

    public function testLiFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler
            ->expects(self::once())
            ->method('liFoStrategy');

        $this->stockOutStrategyService->liFoStrategy();
    }

    public function testHiFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler
            ->expects(self::once())
            ->method('hiFoStrategy');

        $this->stockOutStrategyService->hiFoStrategy();
    }

    public function testLoFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler
            ->expects(self::once())
            ->method('loFoStrategy');

        $this->stockOutStrategyService->loFoStrategy();
    }

    public function testChaoticWarehousingStockOutStrategy(): void
    {
        $this->stockOutStrategyDataHandler
            ->expects(self::once())
            ->method('chaoticStorageStockOutStrategy');

        $this->stockOutStrategyService->chaoticWarehousingStockOutStrategy();
    }
}
