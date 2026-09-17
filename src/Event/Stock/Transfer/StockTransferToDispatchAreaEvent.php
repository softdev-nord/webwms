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
    class: 'StockTransferToDispatchAreaEvent'
)]
class StockTransferToDispatchAreaEvent extends StockTransferBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_transfer_to_dispatch_area';

    public const string EVENT = 'ST104';

    /**
     * ST104 Umlagerung aus LV-Lager in WA-Zone
     */
    public function stockTransferToDispatchArea(Request $request): RedirectResponse|Response
    {
        return $this->handleStockTransfer(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Umlagerung aus LV-Lager in WA-Zone',
            stockTransferActionRoute: 'stock_transfer_to_dispatch_area'
        );
    }
}
