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
    class: 'StockOutFromContainerEvent'
)]
class StockOutFromContainerEvent extends StockOutBookingMethodEvent
{
    public const string EVENT_NAME = 'stock.stock_out_from_container';

    public const string EVENT = 'SO103';

    /**
     * SO103 Auslagern aus Container
     */
    public function stockOutFromContainer(Request $request): RedirectResponse|Response
    {
        return $this->handleStockOut(
            request: $request,
            bookingMethod: self::EVENT,
            pageTitle: 'Auslagern aus Container',
            stockOutActionRoute: 'stock_out_from_container'
        );
    }
}
