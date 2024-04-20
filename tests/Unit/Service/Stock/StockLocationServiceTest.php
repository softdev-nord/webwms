<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLocationEntity;
use WebWMS\Service\DataHandlers\Stock\StockLocationDataHandler;
use WebWMS\Service\Stock\StockLocationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationServiceTest
 */
#[CoversClass(StockLocationService::class)]
final class StockLocationServiceTest extends TestCase
{
    private StockLocationService $stockLocationService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockLocationDataHandler::class);
        $this->stockLocationService = new StockLocationService($this->mockObject);
    }

    public function testGetAllStockLocations(): void
    {
        $stockLocations = ['location1', 'location2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllStockLocation')
            ->willReturn(new JsonResponse($stockLocations));

        $result = $this->stockLocationService->getAllStockLocations();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetStockLocationByCoordinate(): void
    {
        $coordinate = '1,2';

        $stockLocationEntity = new StockLocationEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getStockLocationByCoordinate')
            ->with($coordinate)
            ->willReturn($stockLocationEntity);

        $result = $this->stockLocationService->getStockLocationByCoordinate($coordinate);

        self::assertSame($stockLocationEntity, $result);
    }

    public function testGetStockLocationDetailsById(): void
    {
        $stockLocationId = '123';
        $stockLocationDetails = ['detail1', 'detail2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getStockLocationDetailsById')
            ->with($stockLocationId)
            ->willReturn($stockLocationDetails);

        $result = $this->stockLocationService->getStockLocationDetailsById($stockLocationId);

        self::assertSame($stockLocationDetails, $result);
    }

    public function testAddStockLocation(): void
    {
        $stockLocationEntity = new StockLocationEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addStockLocation')
            ->with($stockLocationEntity);

        $this->stockLocationService->addStockLocation($stockLocationEntity);
    }

    public function testUpdateStockLocation(): void
    {
        $stockLocationEntity = new StockLocationEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateStockLocation')
            ->with($stockLocationEntity);

        $this->stockLocationService->updateStockLocation($stockLocationEntity);
    }

    public function testDeleteStockLocation(): void
    {
        $stockLocationEntity = new StockLocationEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteStockLocation')
            ->with($stockLocationEntity);

        $this->stockLocationService->deleteStockLocation($stockLocationEntity);
    }

    public function testGetAllStockLocationsForSelect(): void
    {
        $expectedResult = ['location1', 'location2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllStockLocationsForSelect')
            ->willReturn($expectedResult);

        $result = $this->stockLocationService->getAllStockLocationsForSelect();

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllStockLocationsAjax(): void
    {
        $expectedResult = ['location1', 'location2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllStockLocationsAjax')
            ->willReturn($expectedResult);

        $result = $this->stockLocationService->getAllStockLocationsAjax();

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllFreeStockLocations(): void
    {
        $stockSystem = 'system1';
        $expectedResult = ['location1', 'location2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllFreeStockLocations')
            ->with($stockSystem)
            ->willReturn($expectedResult);

        $result = $this->stockLocationService->getAllFreeStockLocations($stockSystem);

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllFreeStockLocationsWithLimit(): void
    {
        $stockSystem = 'system1';
        $limit = 10;
        $leQuantity = 500.00;
        $expectedResult = ['location1', 'location2'];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllFreeStockLocationsWithLimit')
            ->with($stockSystem, $limit)
            ->willReturn($expectedResult);

        $result = $this->stockLocationService->getAllFreeStockLocationsWithLimit($stockSystem, $limit, $leQuantity);

        self::assertSame($expectedResult, $result);
    }

    public function testGetFirstFreeStockLocation(): void
    {
        $stockSystem = 'Block-Lager';
        $limit = 10;
        $leQuantity = 500.00;
        $stockLocations = [
            ['location1', 'belegt' => false],
            ['location2', 'belegt' => true],
            ['location3', 'belegt' => false],
        ];
        $expectedResult = [['location1', 'belegt' => false], ['location3', 'belegt' => false]];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllFreeStockLocationsWithLimit')
            ->with($stockSystem, $limit)
            ->willReturn($stockLocations);

        $result = $this->stockLocationService->getFirstFreeStockLocation($stockSystem, $limit, $leQuantity);

        self::assertSame($expectedResult, $result);
    }

    public function testGetRemainder(): void
    {
        $stockLocation = [
            'ln' => '101',
            'fb' => '1',
            'sp' => '1',
            'tf' => '1',
            'koordinate' => '101000100010001',
            'system' => 'Block-Lager',
        ];
        $remainder = 5;
        $expectedResult = [
            [
                'ln' => '101',
                'fb' => '1',
                'sp' => '1',
                'tf' => '1',
                'lnKomplett' => '101-1-1-1',
                'koordinate' => '101000100010001',
                'system' => 'Block-Lager',
                'quantity' => 5,
            ],
        ];

        $result = $this->stockLocationService->getRemainder($stockLocation, $remainder);

        self::assertSame($expectedResult, $result);
    }
}
