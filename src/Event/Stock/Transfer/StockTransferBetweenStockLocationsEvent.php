<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Transfer;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Transfer',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockTransferBetweenStockLocationsEvent'
)]
class StockTransferBetweenStockLocationsEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_transfer_between_stock_locations';

    final public const EVENT = 'ST101';

    /**
     * ST101 Umlagern
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function stockTransferBetweenStockLocations(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
