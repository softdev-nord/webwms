<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Domain;

use DomainException;

final class InsufficientAvailableStockException extends DomainException
{
}
