<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Out;

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
    package: 'WebWMS\Event\Stock\Out',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockOutBookingMethodEvent'
)]
class StockOutBookingMethodEvent extends BaseEvent
{
    public const string EVENT_NAME = 'stock.stock_out';

    public const string EVENT = 'SO101';

    /**
     * SO101 Auslagern direkt
     */
    public function stockOut(Request $request): RedirectResponse|Response
    {
        return $this->handleStockOut(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Auslagern direkt',
            stockOutActionRoute: 'stock_out'
        );
    }

    /**
     * Gemeinsamer Handler für alle Stock-Out Prozesse.
     * Zeigt aktuell einen Platzhalter-Dialog bis zur vollständigen Implementierung.
     */
    protected function handleStockOut(
        Request $request,
        string $bookingMethod,
        string $pageTitle,
        string $stockOutActionRoute
    ): Response {
        $html = $this->twigEnvironment->render(
            'modal/stock_out_placeholder.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => $pageTitle,
                'booking_method' => $bookingMethod,
                'stock_out_action_route' => $stockOutActionRoute,
            ]
        );

        return new Response($html);
    }
}

