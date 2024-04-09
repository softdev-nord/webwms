<?php

declare(strict_types=1);

namespace WebWMS\Event;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\TransportHistory\TransportHistoryService;
use WebWMS\Service\TransportRequest\TransportRequestService;

class BaseEvent extends Event
{
    public function __construct(
        protected RequirementsService $requirementsService,
        protected StockLocationService $stockLocationService,
        protected TransportRequestService $transportRequestService,
        protected TransportHistoryService $transportHistoryService,
        protected FormFactoryInterface $formFactory,
        protected Environment $twigEnvironment
    ) {
    }
}
