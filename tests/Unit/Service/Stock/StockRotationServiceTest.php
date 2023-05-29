<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Service\DataHandlers\Stock\StockRotationDataHandler;
use WebWMS\Service\Stock\StockRotationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockRotationServiceTest
 *
 * @covers \WebWMS\Service\Stock\StockRotationService
 */
final class StockRotationServiceTest extends TestCase
{
    private StockRotationService $stockRotationService;

    /**
     * @var (StockRotationDataHandler&MockObject)|MockObject
     */
    private MockObject|StockRotationDataHandler $stockRotationDataHandler;

    protected function setUp(): void
    {
        $this->stockRotationDataHandler = $this->createMock(StockRotationDataHandler::class);
        $this->stockRotationService = new StockRotationService($this->stockRotationDataHandler);
    }

    public function testGetAllStockRotations(): void
    {
        $expectedResult = [];

        $this->stockRotationDataHandler
            ->expects(self::once())
            ->method('getAllStockRotations')
            ->willReturn($expectedResult);

        $result = $this->stockRotationService->getAllStockRotations();

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllStockRotationsWithJoin(): void
    {
        $expectedResponse = new JsonResponse([]);

        $this->stockRotationDataHandler
            ->expects(self::once())
            ->method('getAllStockRotationsWithJoin')
            ->willThrowException(new \Exception());

        $this->expectException(\Exception::class);

        $result = $this->stockRotationService->getAllStockRotationsWithJoin();

        self::assertEquals($expectedResponse, $result);
    }
}
