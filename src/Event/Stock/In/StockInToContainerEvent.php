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
 * Class        StockInToContainerEvent
 */
class StockInToContainerEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_in_to_container';

    final public const EVENT = 'SI105';

    /**
     * SI105 Einlagern in Container
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockInToContainer(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
