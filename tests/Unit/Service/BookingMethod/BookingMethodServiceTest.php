<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\BookingMethod;

use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use WebWMS\Form\Stock\StockInFinalType;
use WebWMS\Form\Stock\StockInType;
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

    /**
     * @var (StockLocationService&MockObject)|MockObject
     */
    private MockObject|StockLocationService $stockLocationService;

    /**
     * @var (TransportRequestService&MockObject)|MockObject
     */
    private MockObject|TransportRequestService $transportRequestService;

    /**
     * @var (FormFactoryInterface&MockObject)|MockObject
     */
    private MockObject|FormFactoryInterface $formFactory;

    /**
     * @var (Environment&MockObject)|MockObject
     */
    private MockObject|Environment $twig;

    private BookingMethodService $bookingMethodService;

    /**
     * @var (FormInterface&MockObject)|MockObject
     */
    private MockObject|FormInterface $form;

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

    public function testStockInReturnsResponseWhenFormSubmittedAndValid(): void
    {
        $request = $this->createMock(Request::class);

        $requestData = [
            'quantity' => 1000,
            'le_quantity' => 500,
            'standard_loading_equipment' => 'Pal Regal',
            'charge' => 0,
            'article_nr' => '60004'
        ];

        $stockLocations = [
            [
                'id' => 1,
                'ln' => '101',
                'fb' => '1',
                'sp' => '1',
                'tf' => '1',
                'koordinate' => '101000100010001',
                'system' => 'BLOCK',
                'quantity' => '500'
            ],
            [
                'id' => 2,
                'ln' => '101',
                'fb' => '1',
                'sp' => '1',
                'tf' => '2',
                'koordinate' => '101000100010002',
                'system' => 'BLOCK',
                'quantity' => '500'
            ],
        ];

        $this->formFactory
            ->expects(self::exactly(1))
            ->method('create')
            ->withConsecutive([StockInType::class], [StockInFinalType::class])
            ->willReturn($this->form);

        $this->form
            ->expects(self::once())
            ->method('handleRequest')
            ->with($request);

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
            ->willReturn($requestData);

        $this->stockLocationService
            ->expects(self::once())
            ->method('getAllFreeStockLocationsWithLimit')
            ->with('KST', 2)
            ->willReturn($stockLocations);

        $this->transportRequestService
            ->expects(self::once())
            ->method('getLastStockUnit')
            ->willReturn(1);

        $this->twig
            ->expects(self::exactly(1))
            ->method('render')
            ->withConsecutive(
                ['modal/put_into_storage.html.twig', self::anything()],
                ['modal/stock_in_modal.html.twig', self::anything()]
            )
            ->willReturn('');

        $expectedResponse = new Response('');

        self::assertEquals($expectedResponse, $this->bookingMethodService->stockIn($request));
    }
    public function testStockInWithInvalidForm(): void
    {
        $request = $this->createMock(Request::class);

        $this->form
            ->expects(self::once())
            ->method('handleRequest')
            ->with($request);
        $this->form
            ->expects(self::once())
            ->method('isSubmitted')
            ->willReturn(true);
        $this->form
            ->expects(self::once())
            ->method('isValid')
            ->willReturn(false);

        $this->formFactory
            ->expects(self::once())
            ->method('create')
            ->with(StockInType::class)
            ->willReturn($this->form);

        // Mock the Twig environment
        $this->twig
            ->expects(self::once())
            ->method('render')
            ->willReturn('Rendered HTML');

        $response = $this->bookingMethodService->stockIn($request);

        self::assertInstanceOf(Response::class, $response);
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
