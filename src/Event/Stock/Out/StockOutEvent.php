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
    class: 'StockOutEvent'
)]
class StockOutEvent extends StockOutBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_out';

    public const string EVENT = 'SO101';

    /**
     * SO101 Auslagern direkt
     */
    public function stockOut(Request $request): RedirectResponse|Response
    {
        return $this->handleStockOut(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Auslagern direkt',
            stockOutActionRoute: 'stock_out'
        );
    }
}
