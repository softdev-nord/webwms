<?php

declare(strict_types=1);

namespace WebWMS\Event\StockOccupancy;

use WebWMS\Event\BaseEvent;

/**
 * @package:    WebWMS\Event\StockOccupancy
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyCreateEvent
 */
class StockOccupancyCreateEvent extends BaseEvent
{
    final public const EVENT_NAME = 'stock_occupancy.create';
}
