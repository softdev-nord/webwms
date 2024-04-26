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
 * Class        StockInToDispatchAreaEvent
 */
class StockInToDispatchAreaEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_in_to_dispatch_area';

    final public const EVENT = 'SI110';

    /**
     * SI110 Einlagern direkt in WA-Zone
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockInToDispatchArea(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
