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
    class: 'StockLendingUsingCostCentreEvent'
)]
class StockLendingUsingCostCentreEvent extends StockLendingBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_lending_using_cost_centre';

    public const string EVENT = 'SL102';

    /**
     * SL102 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
     */
    public function stockLendingUsingCostCentre(Request $request): RedirectResponse|Response
    {
        return $this->handleStockLending(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Auftrag ausleihe auf Kostenstelle',
            stockLendingActionRoute: 'stock_lending_using_cost_centre'
        );
    }
}
