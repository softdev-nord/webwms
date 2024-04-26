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
 * Class        StockTransferFromCostCentreEvent
 */
class StockTransferFromCostCentreEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_transfer_from_cost_centre';

    final public const EVENT = 'ST102';

    /**
     * ST102 Rückgabe von Kostenstelle
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function stockTransferFromCostCentre(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
