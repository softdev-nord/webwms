<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock\Correction;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock\Correction',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockCorrectionEvent'
)]
class StockCorrectionEvent extends Event
{
    final public const EVENT_NAME = 'stock.stock_correction';

    final public const EVENT = 'SC101';

    /**
     * SC101 Bestandskorrektur
     *
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function stockCorrection(Request $request): RedirectResponse|Response
    {
        // TODO: Implement logic
        return new Response('Example Response');
    }
}
