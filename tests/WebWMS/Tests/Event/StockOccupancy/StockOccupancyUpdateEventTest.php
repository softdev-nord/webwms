<?php

declare(strict_types=1);

namespace WebWMS\Tests\Event\StockOccupancy;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockOccupancy;
use WebWMS\Event\StockOccupancy\StockOccupancyUpdateEvent;
use WebWMS\Service\Stock\StockOccupancyService;

#[CoversClass(StockOccupancyUpdateEvent::class)]
class StockOccupancyUpdateEventTest extends TestCase
{
    private MockObject $stockOccupancyServiceMock;

    private StockOccupancyUpdateEvent $stockOccupancyUpdateEvent;

    protected function setUp(): void
    {
        $this->stockOccupancyServiceMock = $this->createMock(StockOccupancyService::class);
        $this->stockOccupancyUpdateEvent = new StockOccupancyUpdateEvent($this->stockOccupancyServiceMock);
    }

    public function testUpdateStockOccupancyWithValidStockOccupancy(): void
    {
        $stockOccupancy = $this->createMock(StockOccupancy::class);
        $id = 123;

        $this->stockOccupancyServiceMock->method('getStockOccupancyById')
            ->with(self::equalTo($id))
            ->willReturn($stockOccupancy);

        $this->stockOccupancyServiceMock->expects($this->once())
            ->method('updateStockOccupancy')
            ->with(self::equalTo($stockOccupancy));

        $this->stockOccupancyUpdateEvent->updateStockOccupancy($id);
    }

    public function testUpdateStockOccupancyWithNull(): void
    {
        $id = 123;

        $this->stockOccupancyServiceMock->method('getStockOccupancyById')
            ->with(self::equalTo($id))
            ->willReturn(null);

        $this->stockOccupancyServiceMock->expects($this->never())
            ->method('updateStockOccupancy');

        $this->stockOccupancyUpdateEvent->updateStockOccupancy($id);
    }
}
