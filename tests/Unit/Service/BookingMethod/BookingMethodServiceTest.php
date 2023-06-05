<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\BookingMethod;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use WebWMS\Service\BookingMethod\BookingMethodService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportRequestService;

/**
 * @package:    WebWMS\Tests\Unit\Service\BookingMethod
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethodServiceTest
 *
 * @covers \WebWMS\Service\BookingMethod\BookingMethodService
 */
final class BookingMethodServiceTest extends TestCase
{
    private RequirementsService $requirementsService;

    private StockLocationService $stockLocationService;

    private TransportRequestService $transportRequestService;

    private FormFactoryInterface $formFactory;

    /**
     * @var Environment|MockObject
     */
    private MockObject|Environment $twig;

    private BookingMethodService $bookingMethodService;

    protected function setUp(): void
    {
        $this->requirementsService = $this->createMock(RequirementsService::class);
        $this->stockLocationService = $this->createMock(StockLocationService::class);
        $this->transportRequestService = $this->createMock(TransportRequestService::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->bookingMethodService = new BookingMethodService(
            $this->requirementsService,
            $this->stockLocationService,
            $this->transportRequestService,
            $this->formFactory,
            $this->twig
        );
    }

    public function testStockInFromGoodsReceipt(): void
    {
        $result = $this->bookingMethodService->stockInFromGoodsReceipt();
        self::assertNull($result);
    }

    public function testStockInFromProduction(): void
    {
        $result = $this->bookingMethodService->stockInFromProduction();
        self::assertNull($result);
    }

    public function testStockInFromCostCentre(): void
    {
        $result = $this->bookingMethodService->stockInFromCostCentre();
        self::assertNull($result);
    }

    public function testStockInIntoContainer(): void
    {
        $result = $this->bookingMethodService->stockInIntoContainer();
        self::assertNull($result);
    }

    public function testStockInForSupplierOrder(): void
    {
        $result = $this->bookingMethodService->stockInForSupplierOrder();
        self::assertNull($result);
    }

    public function testStockInUsingLoadingEquipment(): void
    {
        $result = $this->bookingMethodService->stockInUsingLoadingEquipment();
        self::assertNull($result);
    }

    public function testStockInIntoReceivingArea(): void
    {
        $result = $this->bookingMethodService->stockInIntoReceivingArea();
        self::assertNull($result);
    }

    public function testStockTransferFromCostCentre(): void
    {
        $result = $this->bookingMethodService->stockTransferFromCostCentre();
        self::assertNull($result);
    }

    public function testStockInIntoCostCentre(): void
    {
        $result = $this->bookingMethodService->stockInIntoCostCentre();
        self::assertNull($result);
    }

    public function testStockInIntoDispatchArea(): void
    {
        $result = $this->bookingMethodService->stockInIntoDispatchArea();
        self::assertNull($result);
    }

    public function testStockOut(): void
    {
        $result = $this->bookingMethodService->stockOut();
        self::assertNull($result);
    }

    public function testStockOutToCostCentre(): void
    {
        $result = $this->bookingMethodService->stockOutToCostCentre();
        self::assertNull($result);
    }

    public function testLendingToCostCentre(): void
    {
        $result = $this->bookingMethodService->lendingToCostCentre();
        self::assertNull($result);
    }

    public function testStockOutFromContainer(): void
    {
        $result = $this->bookingMethodService->stockOutFromContainer();
        self::assertNull($result);
    }

    public function testStockOutFromCostCentre(): void
    {
        $result = $this->bookingMethodService->stockOutFromCostCentre();
        self::assertNull($result);
    }

    public function testStockOutFromDispatchArea(): void
    {
        $result = $this->bookingMethodService->stockOutFromDispatchArea();
        self::assertNull($result);
    }

    public function testStockOutByOrder(): void
    {
        $result = $this->bookingMethodService->stockOutByOrder();
        self::assertNull($result);
    }

    public function testStockOutFromReceivingArea(): void
    {
        $result = $this->bookingMethodService->stockOutFromReceivingArea();
        self::assertNull($result);
    }

    public function testStockOutOrderList(): void
    {
        $result = $this->bookingMethodService->stockOutOrderList();
        self::assertNull($result);
    }

    public function testStockOutUsingCostCentre(): void
    {
        $result = $this->bookingMethodService->stockOutUsingCostCentre();
        self::assertNull($result);
    }

    public function testStockTransferToCostCentre(): void
    {
        $result = $this->bookingMethodService->stockTransferToCostCentre();
        self::assertNull($result);
    }

    public function testStockOutToDispatchArea(): void
    {
        $result = $this->bookingMethodService->stockOutToDispatchArea();
        self::assertNull($result);
    }

    public function testStockOutOrderConsolidationToCostCentre(): void
    {
        $result = $this->bookingMethodService->stockOutOrderConsolidationToCostCentre();
        self::assertNull($result);
    }

    public function testStockTransferBetween(): void
    {
        $result = $this->bookingMethodService->stockTransferBetween();
        self::assertNull($result);
    }

    public function testStockCorrection(): void
    {
        $result = $this->bookingMethodService->stockCorrection();
        self::assertNull($result);
    }

    public function testStockTransferFromReceivingAreaToStock(): void
    {
        $result = $this->bookingMethodService->stockTransferFromReceivingAreaToStock();
        self::assertNull($result);
    }

    public function testStockTransferFromStockToDispatchArea(): void
    {
        $result = $this->bookingMethodService->stockTransferFromStockToDispatchArea();
        self::assertNull($result);
    }
}
