<?php

declare(strict_types=1);

namespace WebWMS\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebWMS\Event\StockOccupancy\StockOccupancyCreateEvent;
use WebWMS\Event\StockOccupancy\StockOccupancyUpdateEvent;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\EventSubscriber',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockOccupancyEventSubscriber'
)]
class StockOccupancyEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            StockOccupancyCreateEvent::EVENT_NAME => 'onStockOccupancyCreate',
            StockOccupancyUpdateEvent::EVENT_NAME => 'onStockOccupancyUpdate',
        ];
    }

    public function onStockOccupancyCreate(StockOccupancyCreateEvent $event): StockOccupancyCreateEvent
    {
        return $event;
    }

    public function onStockOccupancyUpdate(StockOccupancyUpdateEvent $event): StockOccupancyUpdateEvent
    {
        return $event;
    }
}
