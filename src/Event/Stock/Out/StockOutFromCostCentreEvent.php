<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Out;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Out',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockOutFromCostCentreEvent'
)]
class StockOutFromCostCentreEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_out_from_cost_centre';

    final public const EVENT = 'SO104';

    /**
     * SO104 Auslagern aus Kostenstelle
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function stockOutFromCostCentre(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
