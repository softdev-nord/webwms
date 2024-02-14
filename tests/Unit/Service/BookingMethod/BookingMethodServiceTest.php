<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\BookingMethod;

use Doctrine\ORM\EntityNotFoundException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use WebWMS\Form\Stock\StockInType;
use WebWMS\Service\BookingMethod\BookingMethodService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportRequest\TransportRequestService;

/**
 * @package:    WebWMS\Tests\Unit\Service\BookingMethod
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethodServiceTest
 */
#[CoversClass(BookingMethodService::class)]
final class BookingMethodServiceTest extends TestCase
{
    private RequirementsService $requirementsService;

    private MockObject $stockLocationService;

    private MockObject $transportRequestService;

    private MockObject $formFactory;

    private MockObject $twig;

    private BookingMethodService $bookingMethodService;

    private MockObject $form;

    #[Override]
    protected function setUp(): void
    {
        $this->requirementsService = $this->createMock(RequirementsService::class);
        $this->stockLocationService = $this->createMock(StockLocationService::class);
        $this->transportRequestService = $this->createMock(TransportRequestService::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->bookingMethodService = new BookingMethodService(
            $this->requirementsService,
            $this->stockLocationService,
            $this->transportRequestService,
            $this->formFactory,
            $this->twig
        );
    }

    public function testStockInReturnsResponseWhenFormIsNotSubmitted(): void
    {
        $request = $this->createMock(Request::class);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with(StockInType::class)
            ->willReturn($this->createMock(FormInterface::class));

        $this->twig
            ->expects(self::once())
            ->method('render')
            ->willReturn('rendered html');

        $response = $this->bookingMethodService->stockIn($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('rendered html', $response->getContent());
    }

    public function testStockInReturnsResponseWhenFormIsSubmittedAndValid(): void
    {
        $request = $this->createMock(Request::class);

        $this->form
            ->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);
        $this->form
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(true);
        $this->form
            ->expects(self::once())
            ->method('getData')
            ->willReturn([
            'quantity' => 10,
            'leQuantity' => 2,
            'standardLoadingEquipment' => 'KARTON',
            'charge' => 'ABC123',
            'articleNr' => '12345',
        ]);

        $this->formFactory
            ->expects(self::exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($this->form, $this->createMock(FormInterface::class));

        $this->stockLocationService
            ->expects(self::once())
            ->method('getAllFreeStockLocationsWithLimit')
            ->with('Durchlaufregal', 5)
            ->willReturn([
                ['id' => 1, 'ln' => 'ln1', 'fb' => 'fb1', 'sp' => 'sp1', 'tf' => 'tf1', 'koordinate' => '1-1-1-1', 'system' => 'Durchlaufregal'],
                ['id' => 2, 'ln' => 'ln2', 'fb' => 'fb2', 'sp' => 'sp2', 'tf' => 'tf2', 'koordinate' => '2-2-2-2', 'system' => 'Durchlaufregal'],
            ]);

        $this->transportRequestService
            ->expects(self::once())
            ->method('getLastStockUnit')
            ->willReturn(100);

        $this->twig
            ->expects(self::once())
            ->method('render')
            ->willReturn('rendered html');

        $response = $this->bookingMethodService->stockIn($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('rendered html', $response->getContent());
    }

    public function testGetBookingMethodReturnsRedirectResponseWhenBookingMethodIsStockIn(): void
    {
        $bookingMethod = 'stock_in';
        $request = $this->createMock(Request::class);

        self::assertInstanceOf(Response::class, $this->bookingMethodService->getBookingMethod($bookingMethod, $request));
    }

    public function testGetBookingMethodThrowsEntityNotFoundException(): void
    {
        $bookingMethod = 'invalid_booking_method';
        $request = $this->createMock(Request::class);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Buchungsmethode ' . $bookingMethod . ' wurde nicht gefunden!');

        $this->bookingMethodService->getBookingMethod($bookingMethod, $request);
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
