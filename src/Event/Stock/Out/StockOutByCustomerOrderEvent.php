<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Out;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package:    WebWMS\Event\Stock\Out
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockOutByCustomerOrderEvent
 */
class StockOutByCustomerOrderEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_out_by_customer_order';

    final public const EVENT = 'SO106';

    /**
     * SO106 Auftrag auslagern
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockOutByCustomerOrder(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
