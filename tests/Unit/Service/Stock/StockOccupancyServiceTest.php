<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;
use WebWMS\Service\Stock\StockOccupancyService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyServiceTest
 *
 * @covers \WebWMS\Service\Stock\StockOccupancyService
 */
final class StockOccupancyServiceTest extends TestCase
{
    private StockOccupancyService $stockOccupancyService;

    /**
     * @var (StockOccupancyDataHandler&MockObject)|MockObject
     */
    private MockObject|StockOccupancyDataHandler $stockOccupancyDataHandler;

    protected function setUp(): void
    {
        $this->stockOccupancyDataHandler = $this->createMock(StockOccupancyDataHandler::class);
        $this->stockOccupancyService = new StockOccupancyService($this->stockOccupancyDataHandler);
    }

    public function testGetAllStockOccupancy(): void
    {
        $expectedResult = [['id' => 1], ['id' => 2]];

        $this->stockOccupancyDataHandler
            ->expects(self::once())
            ->method('getAllStockOccupancy')
            ->willReturn($expectedResult);

        $result = $this->stockOccupancyService->getAllStockOccupancy();

        self::assertSame($expectedResult, $result);
    }

    public function testGetStockOccupancyByCoordinate(): void
    {
        $stockLocationCoordinate = 'ABC';
        $request = new Request([], [], ['stock_location_coordinate' => $stockLocationCoordinate], [], [], []);

        $stockOccupancies = [
            ['id' => 1, 'koordinate' => 'XYZ'],
            ['id' => 2, 'koordinate' => 'ABC'],
            ['id' => 3, 'koordinate' => 'ABC'],
        ];
        $expectedResult = [
            ['id' => 2, 'koordinate' => 'ABC'],
            ['id' => 3, 'koordinate' => 'ABC'],
        ];

        $this->stockOccupancyDataHandler
            ->expects(self::once())
            ->method('getAllStockOccupancy')
            ->willReturn($stockOccupancies);

        $result = $this->stockOccupancyService->getStockOccupancyByCoordinate($request);

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllStockOccupancyByLn(): void
    {
        $stockLocationLn = 123;
        $expectedResult = [['id' => 1], ['id' => 2]];

        $this->stockOccupancyDataHandler
            ->expects(self::once())
            ->method('getStockOccupancy')
            ->with($stockLocationLn)
            ->willReturn($expectedResult);

        $result = $this->stockOccupancyService->getAllStockOccupancyByLn($stockLocationLn);

        self::assertSame($expectedResult, $result);
    }

    public function testGetStockOccupancyByArticleNr(): void
    {
        $articleNr = 456;
        $expectedResult = ['id' => 1, 'articleNr' => 456];

        $this->stockOccupancyDataHandler
            ->expects(self::once())
            ->method('getStockOccupancyByArticleNr')
            ->with($articleNr)
            ->willReturn($expectedResult);

        $result = $this->stockOccupancyService->getStockOccupancyByArticleNr($articleNr);

        self::assertSame($expectedResult, $result);
    }
}
