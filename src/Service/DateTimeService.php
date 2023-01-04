<?php

declare(strict_types=1);

namespace WebWMS\Service;

class DateTimeService
{
    public function createDateTime(): \DateTime
    {
        return new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));
    }
}
