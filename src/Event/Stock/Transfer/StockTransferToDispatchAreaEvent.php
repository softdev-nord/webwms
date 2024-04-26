<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Transfer;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @package:    WebWMS\Event\Stock\Transfer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockTransferToDispatchAreaEvent
 */
class StockTransferToDispatchAreaEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_transfer_to_dispatch_area';

    final public const EVENT = 'ST104';

    /**
     * ST104 Umlagerung aus LV-Lager in WA-Zone
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockTransferToDispatchArea(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
