<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Lending;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Lending',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLendingToCostCentreEvent'
)]
class StockLendingToCostCentreEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_lending_to_cost_centre';

    final public const EVENT = 'SL101';

    /**
     * SL101 Ausleihen auf Kostenstelle
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function stockLendingToCostCentre(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
