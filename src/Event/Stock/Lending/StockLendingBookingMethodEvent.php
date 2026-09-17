<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Lending;

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
    package: 'WebWMS\Event\Stock\Lending',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLendingBookingMethodEvent'
)]
class StockLendingBookingMethodEvent extends BaseEvent
{
    public const string EVENT_NAME = 'stock.stock_lending_to_cost_centre';

    public const string EVENT = 'SL101';

    /**
     * SL101 Ausleihen auf Kostenstelle
     */
    public function stockLendingToCostCentre(Request $request): RedirectResponse|Response
    {
        return $this->handleStockLending(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Ausleihen auf Kostenstelle',
            stockLendingActionRoute: 'stock_lending_to_cost_centre'
        );
    }

    /**
     * Gemeinsamer Handler für alle Stock-Lending Prozesse.
     * Zeigt aktuell einen Platzhalter-Dialog bis zur vollständigen Implementierung.
     */
    protected function handleStockLending(
        Request $request,
        string $bookingMethod,
        string $pageTitle,
        string $stockLendingActionRoute
    ): Response {
        $html = $this->twigEnvironment->render(
            'modal/stock_lending_placeholder.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => $pageTitle,
                'booking_method' => $bookingMethod,
                'stock_lending_action_route' => $stockLendingActionRoute,
            ]
        );

        return new Response($html);
    }
}

