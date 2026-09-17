<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Transfer;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Transfer',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockTransferFromCostCentreEvent'
)]
class StockTransferFromCostCentreEvent extends StockTransferBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_transfer_from_cost_centre';

    public const string EVENT = 'ST102';

    /**
     * ST102 Rückgabe von Kostenstelle
     */
    public function stockTransferFromCostCentre(Request $request): RedirectResponse|Response
    {
        return $this->handleStockTransfer(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Rückgabe von Kostenstelle',
            stockTransferActionRoute: 'stock_transfer_from_cost_centre'
        );
    }
}
