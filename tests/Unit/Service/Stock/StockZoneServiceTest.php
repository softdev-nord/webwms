<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Stock\StockZoneDataHandler;
use WebWMS\Service\Stock\StockZoneService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockZoneServiceTest'
)]
#[CoversClass(StockZoneService::class)]
final class StockZoneServiceTest extends TestCase
{
    private StockZoneService $stockZoneService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockZoneDataHandler::class);
        $this->stockZoneService = new StockZoneService($this->mockObject);
    }

    public function testGetAllStockZones(): void
    {
        $jsonResponse = new JsonResponse([]);

        $this->mockObject
            ->expects($this->once())
            ->method('getAllStockZones')
            ->willReturn($jsonResponse);

        $result = $this->stockZoneService->getAllStockZones();

        self::assertEquals($jsonResponse, $result);
    }

    public function testGetStockZoneById(): void
    {
        $stockZoneId = 1;
        $expectedStockZone = new StockZone();

        $this->mockObject
            ->expects($this->once())
            ->method('getStockZoneById')
            ->with($stockZoneId)
            ->willReturn($expectedStockZone);

        $result = $this->stockZoneService->getStockZoneById($stockZoneId);

        self::assertEquals($expectedStockZone, $result);
    }

    public function testAddStockZone(): void
    {
        $stockZone = new StockZone();

        $this->mockObject
            ->expects($this->once())
            ->method('addStockZone')
            ->with($stockZone);

        $this->stockZoneService->addStockZone($stockZone);
    }

    public function testUpdateStockZone(): void
    {
        $stockZone = new StockZone();

        $this->mockObject
            ->expects($this->once())
            ->method('updateStockZone')
            ->with($stockZone);

        $this->stockZoneService->updateStockZone($stockZone);
    }

    public function testDeleteStockZone(): void
    {
        $stockZone = new StockZone();

        $this->mockObject
            ->expects($this->once())
            ->method('deleteStockZone')
            ->with($stockZone);

        $this->stockZoneService->deleteStockZone($stockZone);
    }
}
