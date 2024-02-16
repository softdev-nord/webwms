<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;
use WebWMS\Service\Stock\StockOccupancyService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyServiceTest
 */
#[CoversClass(StockOccupancyService::class)]
final class StockOccupancyServiceTest extends TestCase
{
    private StockOccupancyService $stockOccupancyService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockOccupancyDataHandler::class);
        $this->stockOccupancyService = new StockOccupancyService($this->mockObject);
    }

    public function testGetAllStockOccupancy(): void
    {
        $jsonResponse = new JsonResponse([['id' => 1], ['id' => 2]]);

        $this->mockObject
            ->expects(self::once())
            ->method('getAllStockOccupancy')
            ->willReturn($jsonResponse);

        $result = $this->stockOccupancyService->getAllStockOccupancy();

        self::assertSame($jsonResponse, $result);
    }

    public function testGetStockOccupancyByCoordinate(): void
    {
        $stockLocationCoordinate = 'ABC';
        $request = new Request([], [], ['stock_location_coordinate' => $stockLocationCoordinate], [], [], []);

        $jsonResponse = new JsonResponse(
            [
                ['id' => 1, 'koordinate' => 'XYZ'],
                ['id' => 2, 'koordinate' => 'ABC'],
                ['id' => 3, 'koordinate' => 'ABC'],
            ]
        );

        $expectedResult = [
            ['id' => 2, 'koordinate' => 'ABC'],
            ['id' => 3, 'koordinate' => 'ABC'],
        ];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllStockOccupancy')
            ->willReturn($jsonResponse);

        $result = $this->stockOccupancyService->getStockOccupancyByCoordinate($request);

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllStockOccupancyByLn(): void
    {
        $stockLocationLn = 123;
        $expectedResult = [['id' => 1], ['id' => 2]];

        $this->mockObject
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

        $this->mockObject
            ->expects(self::once())
            ->method('getStockOccupancyByArticleNr')
            ->with($articleNr)
            ->willReturn($expectedResult);

        $result = $this->stockOccupancyService->getStockOccupancyByArticleNr($articleNr);

        self::assertSame($expectedResult, $result);
    }
}
