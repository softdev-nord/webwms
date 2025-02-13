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
    class: 'StockInToCostCentreEvent'
)]
class StockInToCostCentreEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_in_to_cost_centre';

    final public const EVENT = 'SI109';

    /**
     * SI109 Einlagern direkt in Kostenstelle
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function stockInToCostCentre(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
