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
    class: 'StockTransferBetweenStockLocationsEvent'
)]
class StockTransferBetweenStockLocationsEvent extends StockTransferBookingMethodEvent
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
}
