<?php

declare(strict_types=1);

namespace WebWMS\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebWMS\Event\StockOccupancy\StockOccupancyCreateEvent;
use WebWMS\Event\StockOccupancy\StockOccupancyUpdateEvent;

/**
 * @package:    WebWMS\EventSubscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyEventSubscriber
 */
class StockOccupancyEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            StockOccupancyCreateEvent::EVENT_NAME => 'onStockOccupancyCreate',
            StockOccupancyUpdateEvent::EVENT_NAME => 'onStockOccupancyDelete',
        ];
    }

    public function onStockOccupancyCreate(StockOccupancyCreateEvent $event)
    {

    }

    public function onStockOccupancyDelete(StockOccupancyCreateEvent $event)
    {

    }
}
