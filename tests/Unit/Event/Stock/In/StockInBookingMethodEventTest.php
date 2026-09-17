<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Event\Stock\In;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use WebWMS\Event\Stock\In\StockInBookingMethodEvent;
use WebWMS\Event\Stock\In\StockInFromGoodsReceiptEvent;
use WebWMS\Event\Stock\In\StockInFromProductionEvent;
use WebWMS\Event\Stock\In\StockInForSupplierOrderEvent;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportHistory\TransportHistoryService;
use WebWMS\Service\TransportRequest\TransportRequestService;

#[CoversClass(StockInBookingMethodEvent::class)]
#[CoversClass(StockInFromGoodsReceiptEvent::class)]
#[CoversClass(StockInFromProductionEvent::class)]
#[CoversClass(StockInForSupplierOrderEvent::class)]
final class StockInBookingMethodEventTest extends TestCase
{
    #[DataProvider('provideStockInEvents')]
    public function testEventsDelegateToSharedHandlerWithCorrectMetadata(
        string $methodName,
        string $expectedBookingMethod,
        string $expectedPageTitle,
        string $expectedRoute
    ): void {
        $event = $this->createTestEventWithCaptureHandler();
        $request = new Request();
        $response = $event->$methodName($request);

        self::assertSame('ok', $response->getContent());
        self::assertSame($expectedBookingMethod, $event->capturedBookingMethod);
        self::assertSame($expectedPageTitle, $event->capturedPageTitle);
        self::assertSame($expectedRoute, $event->capturedRoute);
    }

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function provideStockInEvents(): array
    {
        return [
            'SI101 Einlagern direkt' => [
                'stockIn',
                'SI101',
                'Einlagern direkt',
                'stock_in',
            ],
            'SI102 Zugang aus Wareneingang' => [
                'stockInFromGoodsReceipt',
                'SI102',
                'Zugang aus Wareneingang',
                'stock_in_from_goods_receipt',
            ],
            'SI103 Zugang aus Produktion' => [
                'stockInFromProduction',
                'SI103',
                'Zugang aus Produktion',
                'stock_in_from_production',
            ],
            'SI106 WE zur Bestellung' => [
                'stockInForSupplierOrder',
                'SI106',
                'WE zur Bestellung',
                'stock_in_for_supplier_order',
            ],
        ];
    }

    private function createTestEventWithCaptureHandler(): StockInBookingMethodEvent
    {
        return new class(
            $this->createStub(RequirementsService::class),
            $this->createStub(StockLocationService::class),
            $this->createStub(TransportRequestService::class),
            $this->createStub(TransportHistoryService::class),
            $this->createStub(FormFactoryInterface::class),
            $this->createStub(Environment::class)
        ) extends StockInBookingMethodEvent {
            public string $capturedBookingMethod = '';
            public string $capturedPageTitle = '';
            public string $capturedRoute = '';

            protected function handleStockIn(
                Request $request,
                string $bookingMethod,
                string $pageTitle,
                string $stockInActionRoute
            ): Response {
                $this->capturedBookingMethod = $bookingMethod;
                $this->capturedPageTitle = $pageTitle;
                $this->capturedRoute = $stockInActionRoute;

                return new Response('ok');
            }
        };
    }
}
