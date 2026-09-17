<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum AllocationTransitionType: string
{
    case Release = 'released';
    case Consume = 'consumed';
}
