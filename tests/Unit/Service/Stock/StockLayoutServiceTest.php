<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\StockLayout;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Stock\StockLayoutDataHandler;
use WebWMS\Service\Stock\StockLayoutService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockLayoutServiceTest'
)]
#[CoversClass(StockLayoutService::class)]
final class StockLayoutServiceTest extends TestCase
{
    private StockLayoutService $stockLayoutService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockLayoutDataHandler::class);
        $this->stockLayoutService = new StockLayoutService($this->mockObject);
    }

    public function testGetAllStockLayouts(): void
    {
        $stockLayouts = ['layout1', 'layout2'];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllStockLayouts')
            ->willReturn(new JsonResponse($stockLayouts));

        $jsonResponse = $this->stockLayoutService->getAllStockLayouts();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetStockLayoutById(): void
    {
        $stockLayoutId = 1;
        $stockLayout = new StockLayout();

        $this->mockObject
            ->expects($this->once())
            ->method('getStockLayoutById')
            ->with($stockLayoutId)
            ->willReturn($stockLayout);

        $result = $this->stockLayoutService->getStockLayoutById($stockLayoutId);

        self::assertSame($stockLayout, $result);
    }

    public function testAddStockLayout(): void
    {
        $stockLayout = new StockLayout();
        $this->mockObject
            ->expects($this->once())
            ->method('addStockLayout')
            ->with($stockLayout);

        $this->stockLayoutService->addStockLayout($stockLayout);
    }

    public function testUpdateStockLayout(): void
    {
        $stockLayout = new StockLayout();
        $this->mockObject
            ->expects($this->once())
            ->method('updateStockLayout')
            ->with($stockLayout);

        $this->stockLayoutService->updateStockLayout($stockLayout);
    }

    public function testDeleteStockLayout(): void
    {
        $stockLayout = new StockLayout();
        $this->mockObject
            ->expects($this->once())
            ->method('deleteStockLayout')
            ->with($stockLayout);

        $this->stockLayoutService->deleteStockLayout($stockLayout);
    }
}
