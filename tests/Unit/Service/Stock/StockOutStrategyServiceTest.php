<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\DataHandlers\StockOutStrategyDataHandler;
use WebWMS\Service\Stock\StockOutStrategyService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOutStrategyServiceTest
 */
#[CoversClass(StockOutStrategyService::class)]
final class StockOutStrategyServiceTest extends TestCase
{
    private StockOutStrategyService $stockOutStrategyService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockOutStrategyDataHandler::class);
        $this->stockOutStrategyService = new StockOutStrategyService($this->mockObject);
    }

    public function testFiFoStrategy(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('fiFoStrategy');

        $this->stockOutStrategyService->fiFoStrategy();
    }

    public function testFeFoStrategy(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('feFoStrategy');

        $this->stockOutStrategyService->feFoStrategy();
    }

    public function testLiFoStrategy(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('liFoStrategy');

        $this->stockOutStrategyService->liFoStrategy();
    }

    public function testHiFoStrategy(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('hiFoStrategy');

        $this->stockOutStrategyService->hiFoStrategy();
    }

    public function testLoFoStrategy(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('loFoStrategy');

        $this->stockOutStrategyService->loFoStrategy();
    }

    public function testChaoticWarehousingStockOutStrategy(): void
    {
        $this->mockObject
            ->expects(self::once())
            ->method('chaoticStorageStockOutStrategy');

        $this->stockOutStrategyService->chaoticWarehousingStockOutStrategy();
    }
}
