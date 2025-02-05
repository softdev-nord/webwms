<?php

declare(strict_types=1);

namespace WebWMS\Service;

use DateTime;
use DateTimeZone;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'DateTimeService'
)]
class DateTimeService
{
    public function createDateTime(): DateTime
    {
        return new DateTime('NOW', new DateTimeZone('Europe/Berlin'));
    }
}
