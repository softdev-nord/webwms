<?php

declare(strict_types=1);

namespace WebWMS\Tests\Event\StockOccupancy;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebWMS\Event\StockOccupancy\StockOccupancyCreateEvent;
use WebWMS\Entity\StockOccupancy;
use WebWMS\Service\Stock\StockOccupancyService;

#[CoversClass(StockOccupancyCreateEvent::class)]
class StockOccupancyCreateEventTest extends TestCase
{
    private MockObject $stockOccupancyServiceMock;

    private StockOccupancyCreateEvent $stockOccupancyCreateEvent;

    protected function setUp(): void
    {
        $this->stockOccupancyServiceMock = $this->createMock(StockOccupancyService::class);
        $this->stockOccupancyCreateEvent = new StockOccupancyCreateEvent($this->stockOccupancyServiceMock);
    }


    public function testCreateStockOccupancyWithValidId(): void
    {
        $stockOccupancy = $this->createMock(StockOccupancy::class);
        $stockServiceMock = $this->createMock(StockOccupancyService::class);
        $stockServiceMock->method('getStockOccupancyById')
                         ->willReturn($stockOccupancy);

        $stockServiceMock->expects($this->once())
                         ->method('addStockOccupancy')
                         ->with(self::equalTo($stockOccupancy));

        $event = new StockOccupancyCreateEvent($stockServiceMock);
        $event->createStockOccupancy(1);
    }

    public function testCreateStockOccupancyWithInvalidId(): void
    {
        $stockServiceMock = $this->createMock(StockOccupancyService::class);
        $stockServiceMock->method('getStockOccupancyById')
                         ->willReturn(null);

        $stockServiceMock->expects($this->never())
                         ->method('addStockOccupancy');

        $event = new StockOccupancyCreateEvent($stockServiceMock);
        $event->createStockOccupancy(999);
    }
}
