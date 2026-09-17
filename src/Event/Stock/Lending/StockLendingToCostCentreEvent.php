<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Lending;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Lending',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLendingToCostCentreEvent'
)]
class StockLendingToCostCentreEvent extends StockLendingBookingMethodEvent
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
}
