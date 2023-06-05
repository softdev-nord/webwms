<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZone;
use WebWMS\Service\DataHandlers\Stock\StockZoneDataHandler;
use WebWMS\Service\Stock\StockZoneService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneServiceTest
 *
 * @covers \WebWMS\Service\Stock\StockZoneService
 */
final class StockZoneServiceTest extends TestCase
{
    private StockZoneService $stockZoneService;

    /**
     * @var (StockZoneDataHandler&MockObject)|MockObject
     */
    private MockObject|StockZoneDataHandler $stockZoneDataHandler;

    protected function setUp(): void
    {
        $this->stockZoneDataHandler = $this->createMock(StockZoneDataHandler::class);
        $this->stockZoneService = new StockZoneService($this->stockZoneDataHandler);
    }

    public function testGetAllStockZones(): void
    {
        $expectedResponse = new JsonResponse([]);

        $this->stockZoneDataHandler
            ->expects(self::once())
            ->method('getAllStockZones')
            ->willReturn($expectedResponse);

        $result = $this->stockZoneService->getAllStockZones();

        self::assertEquals($expectedResponse, $result);
    }

    public function testGetStockZoneById(): void
    {
        $stockZoneId = 1;
        $expectedStockZone = new StockZone();

        $this->stockZoneDataHandler
            ->expects(self::once())
            ->method('getStockZoneById')
            ->with($stockZoneId)
            ->willReturn($expectedStockZone);

        $result = $this->stockZoneService->getStockZoneById($stockZoneId);

        self::assertEquals($expectedStockZone, $result);
    }

    public function testAddStockZone(): void
    {
        $stockZone = new StockZone();

        $this->stockZoneDataHandler
            ->expects(self::once())
            ->method('addStockZone')
            ->with($stockZone);

        $this->stockZoneService->addStockZone($stockZone);
    }

    public function testUpdateStockZone(): void
    {
        $stockZone = new StockZone();

        $this->stockZoneDataHandler
            ->expects(self::once())
            ->method('updateStockZone')
            ->with($stockZone);

        $this->stockZoneService->updateStockZone($stockZone);
    }

    public function testDeleteStockZone(): void
    {
        $stockZone = new StockZone();

        $this->stockZoneDataHandler
            ->expects(self::once())
            ->method('deleteStockZone')
            ->with($stockZone);

        $this->stockZoneService->deleteStockZone($stockZone);
    }
}
