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
    class: 'StockOutFromDispatchAreaEvent'
)]
class StockOutFromDispatchAreaEvent extends StockOutBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_out_from_dispatch_area';

    public const string EVENT = 'SO105';

    /**
     * SO105 Auslagern direkt aus WA-Zone
     */
    public function stockOutFromDispatchArea(Request $request): RedirectResponse|Response
    {
        return $this->handleStockOut(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Auslagern direkt aus WA-Zone',
            stockOutActionRoute: 'stock_out_from_dispatch_area'
        );
    }
}
