<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Stock;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Stock\StockRotationDataHandler;
use WebWMS\Service\Stock\StockRotationService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockRotationServiceTest'
)]
#[CoversClass(StockRotationService::class)]
final class StockRotationServiceTest extends TestCase
{
    private StockRotationService $stockRotationService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(StockRotationDataHandler::class);
        $this->stockRotationService = new StockRotationService($this->mockObject);
    }

    public function testGetAllStockRotations(): void
    {
        $expectedResult = [];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllStockRotations')
            ->willReturn($expectedResult);

        $result = $this->stockRotationService->getAllStockRotations();

        self::assertSame($expectedResult, $result);
    }

    public function testGetAllStockRotationsWithJoin(): void
    {
        $jsonResponse = new JsonResponse([]);

        $this->mockObject
            ->expects($this->once())
            ->method('getAllStockRotationsWithJoin')
            ->willThrowException(new Exception());

        $this->expectException(Exception::class);

        $result = $this->stockRotationService->getAllStockRotationsWithJoin();

        self::assertEquals($jsonResponse, $result);
    }
}
