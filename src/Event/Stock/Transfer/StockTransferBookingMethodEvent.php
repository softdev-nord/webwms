<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Transfer;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use WebWMS\Event\BaseEvent;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportHistory\TransportHistoryService;
use WebWMS\Service\TransportRequest\TransportRequestService;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Transfer',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockTransferBookingMethodEvent'
)]
class StockTransferBookingMethodEvent extends BaseEvent
{
    public const string EVENT_NAME = 'stock.stock_transfer_between_stock_locations';

    public const string EVENT = 'ST101';

    /**
     * ST101 Umlagern
     */
    public function stockTransferBetweenStockLocations(Request $request): RedirectResponse|Response
    {
        return $this->handleStockTransfer(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Umlagern',
            stockTransferActionRoute: 'stock_transfer_between_stock_locations'
        );
    }

    /**
     * Gemeinsamer Handler für alle Stock-Transfer Prozesse.
     * Zeigt aktuell einen Platzhalter-Dialog bis zur vollständigen Implementierung.
     */
    protected function handleStockTransfer(
        Request $request,
        string $bookingMethod,
        string $pageTitle,
        string $stockTransferActionRoute
    ): Response {
        $html = $this->twigEnvironment->render(
            'modal/stock_transfer_placeholder.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => $pageTitle,
                'booking_method' => $bookingMethod,
                'stock_transfer_action_route' => $stockTransferActionRoute,
            ]
        );

        return new Response($html);
    }
}

