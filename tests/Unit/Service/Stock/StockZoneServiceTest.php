<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Service\DataHandlers\Stock\StockZoneDataHandler;
use WebWMS\Service\Stock\StockZoneService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneServiceTest
 */
#[CoversClass(StockZoneService::class)]
final class StockZoneServiceTest extends TestCase
{
    private StockZoneService $stockZoneService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockZoneDataHandler::class);
        $this->stockZoneService = new StockZoneService($this->mockObject);
    }

    public function testGetAllStockZones(): void
    {
        $jsonResponse = new JsonResponse([]);

        $this->mockObject
            ->expects(self::once())
            ->method('getAllStockZones')
            ->willReturn($jsonResponse);

        $result = $this->stockZoneService->getAllStockZones();

        self::assertEquals($jsonResponse, $result);
    }

    public function testGetStockZoneById(): void
    {
        $stockZoneId = 1;
        $stockZoneEntity = new StockZoneEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getStockZoneById')
            ->with($stockZoneId)
            ->willReturn($stockZoneEntity);

        $result = $this->stockZoneService->getStockZoneById($stockZoneId);

        self::assertEquals($stockZoneEntity, $result);
    }

    public function testAddStockZone(): void
    {
        $stockZoneEntity = new StockZoneEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('addStockZone')
            ->with($stockZoneEntity);

        $this->stockZoneService->addStockZone($stockZoneEntity);
    }

    public function testUpdateStockZone(): void
    {
        $stockZoneEntity = new StockZoneEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('updateStockZone')
            ->with($stockZoneEntity);

        $this->stockZoneService->updateStockZone($stockZoneEntity);
    }

    public function testDeleteStockZone(): void
    {
        $stockZoneEntity = new StockZoneEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('deleteStockZone')
            ->with($stockZoneEntity);

        $this->stockZoneService->deleteStockZone($stockZoneEntity);
    }
}
