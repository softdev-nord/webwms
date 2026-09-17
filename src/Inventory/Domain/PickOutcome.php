<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

enum PickOutcome: string
{
    case Picked = 'picked';
    case Shortage = 'shortage';
}
