<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Out;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Out',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockOutByCustomerOrderEvent'
)]
class StockOutByCustomerOrderEvent extends StockOutBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_out_by_customer_order';

    public const string EVENT = 'SO106';

    /**
     * SO106 Auftrag auslagern
     */
    public function stockOutByCustomerOrder(Request $request): RedirectResponse|Response
    {
        return $this->handleStockOut(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Auftrag auslagern',
            stockOutActionRoute: 'stock_out_by_customer_order'
        );
    }
}
