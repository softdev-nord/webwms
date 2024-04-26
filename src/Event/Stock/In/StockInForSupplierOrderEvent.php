<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\In;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package:    WebWMS\Event\Stock\In
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockInForSupplierOrderEvent
 */
class StockInForSupplierOrderEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_in_for_supplier_order';

    final public const EVENT = 'SI106';

    /**
     * SI106 WE zur Bestellung
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockInForSupplierOrder(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
