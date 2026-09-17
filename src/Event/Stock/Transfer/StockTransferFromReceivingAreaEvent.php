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
    class: 'StockTransferFromReceivingAreaEvent'
)]
class StockTransferFromReceivingAreaEvent extends StockTransferBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_transfer_from_receiving_area';

    public const string EVENT = 'ST103';

    /**
     * ST103 Umlagerung aus WE-Zone ins LV-Lager
     */
    public function stockTransferFromReceivingArea(Request $request): RedirectResponse|Response
    {
        return $this->handleStockTransfer(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Umlagerung aus WE-Zone ins LV-Lager',
            stockTransferActionRoute: 'stock_transfer_from_receiving_area'
        );
    }
}
