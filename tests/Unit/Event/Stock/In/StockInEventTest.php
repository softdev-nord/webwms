<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Event\Stock\In;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use WebWMS\Event\Stock\In\StockInEvent;
use WebWMS\Form\Stock\StockInType;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportHistory\TransportHistoryService;
use WebWMS\Service\TransportRequest\TransportRequestService;

/**
 * @package:    WebWMS\Tests\Unit\Event\Stock\In
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockInEventTest
 */
#[CoversClass(StockInEvent::class)]
class StockInEventTest extends TestCase
{
    private RequirementsService $requirementsService;

    private MockObject $stockLocationService;

    private MockObject $transportRequestService;

    private MockObject $transportHistoryService;

    private MockObject $formFactory;

    private MockObject $twig;

    private StockInEvent $stockInEvent;

    private MockObject $form;

    protected function setUp(): void
    {
        $this->requirementsService = $this->createMock(RequirementsService::class);
        $this->stockLocationService = $this->createMock(StockLocationService::class);
        $this->transportRequestService = $this->createMock(TransportRequestService::class);
        $this->transportHistoryService = $this->createMock(TransportHistoryService::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->stockInEvent = new StockInEvent(
            $this->requirementsService,
            $this->stockLocationService,
            $this->transportRequestService,
            $this->transportHistoryService,
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

        $response = $this->stockInEvent->stockIn($request);

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
                'quantity' => '10',
                'leQuantity' => '2',
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
                ['id' => 1, 'ln' => 101, 'fb' => 1, 'sp' => 1, 'tf' => 1, 'koordinate' => '1-1-1-1', 'system' => 'Durchlaufregal'],
                ['id' => 2, 'ln' => 101, 'fb' => 1, 'sp' => 1, 'tf' => 2, 'koordinate' => '2-2-2-2', 'system' => 'Durchlaufregal'],
            ]);

        $this->transportRequestService
            ->expects(self::once())
            ->method('getLastStockUnit')
            ->willReturn([]);

        $this->twig
            ->expects(self::once())
            ->method('render')
            ->willReturn('rendered html');

        $response = $this->stockInEvent->stockIn($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertEquals('rendered html', $response->getContent());
    }
}
