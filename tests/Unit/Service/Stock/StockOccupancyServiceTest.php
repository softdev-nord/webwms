<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Service\DataHandlers\Stock\StockOccupancyDataHandler;
use WebWMS\Service\DateTimeService;
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

    private DateTimeService $dateTimeService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockOccupancyDataHandler::class);
        $this->dateTimeService = new DateTimeService();
        $this->stockOccupancyService = new StockOccupancyService($this->mockObject, $this->dateTimeService);
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
        $stockLocationCoordinate = '101000100010001';
        $request = new Request(
            [],
            [],
            [
                'stock_location_coordinate' => $stockLocationCoordinate,
            ],
            [],
            [],
            []
        );

        $expectedResult =
            [
                [
                    'article_nr' => '60004',
                    'stock_coordinate' => '101000100010001',
                    'article_name' => 'Telefon MBO Alpha 1600 CT',
                    'incoming_stock' => '480.00',
                    'reserved_stock' => '0.00',
                    'in_stock' => '0.00',
                    'last_incoming' => '2024-04-10 17:08:33',
                    'last_outgoing' => null,
                ],
            ];

        $this->mockObject
            ->expects(self::once())
            ->method('getStockOccupancyByCoordinate')
            ->willReturn($expectedResult);

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

    public function testGetStockOccupancyByArticleId(): void
    {
        $articleId = 1;
        $expectedResult = ['id' => 1, 'articleNr' => 456];

        $this->mockObject
            ->expects(self::once())
            ->method('getStockOccupancyByArticleId')
            ->with($articleId)
            ->willReturn($expectedResult);

        $result = $this->stockOccupancyService->getStockOccupancyByArticleId($articleId);

        self::assertSame($expectedResult, $result);
    }
}
