<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\In;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\In',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInForSupplierOrderEvent'
)]
class StockInForSupplierOrderEvent extends StockInBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_in_for_supplier_order';

    public const string EVENT = 'SI106';

    /**
     * SI106 WE zur Bestellung
     */
    public function stockInForSupplierOrder(Request $request): RedirectResponse|Response
    {
        return $this->handleStockIn(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'WE zur Bestellung',
            stockInActionRoute: 'stock_in_for_supplier_order'
        );
    }
}
