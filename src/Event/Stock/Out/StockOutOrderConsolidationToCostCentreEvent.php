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
 * Class        StockOutOrderConsolidationToCostCentreEvent
 */
class StockOutOrderConsolidationToCostCentreEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_out_order_consolidation_to_cost_centre';

    final public const EVENT = 'SO111';

    /**
     * SO111 Sammelkommissionierung auf Kostenstelle
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockOutOrderConsolidationToCostCentre(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
