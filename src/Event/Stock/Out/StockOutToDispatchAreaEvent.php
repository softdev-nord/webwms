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
 * Class        StockOutToDispatchAreaEvent
 */
class StockOutToDispatchAreaEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_out_to_dispatch_area';

    final public const EVENT = 'SO110';

    /**
     * SO110 Auftrag auslagern in WA-Zone
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockOutToDispatchArea(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
