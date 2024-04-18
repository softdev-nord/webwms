<?php

declare(strict_types=1);

namespace WebWMS\Event;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Stock\StockOccupancyService;
use WebWMS\Service\TransportHistory\TransportHistoryService;
use WebWMS\Service\TransportRequest\TransportRequestService;

/**
 * @package:    WebWMS\Event
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BaseEvent
 */
class BaseEvent extends Event
{
    public function __construct(
        protected RequirementsService $requirementsService,
        protected StockLocationService $stockLocationService,
#        protected StockOccupancyService $stockOccupancyService,
        protected TransportRequestService $transportRequestService,
        protected TransportHistoryService $transportHistoryService,
        protected FormFactoryInterface $formFactory,
        protected Environment $twigEnvironment
    ) {
    }
}
