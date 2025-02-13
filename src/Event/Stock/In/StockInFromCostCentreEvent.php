<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\In;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\In',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInFromCostCentreEvent'
)]
class StockInFromCostCentreEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_in_from_cost_centre';

    final public const EVENT = 'SI104';

    /**
     * SI104 Rückgabe von Kostenstelle
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function stockInFromCostCentre(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
