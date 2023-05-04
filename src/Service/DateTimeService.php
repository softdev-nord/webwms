<?php

declare(strict_types=1);

namespace WebWMS\Service;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DateTimeService
 */
class DateTimeService
{
    public function createDateTime(): \DateTime
    {
        return new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));
    }
}
