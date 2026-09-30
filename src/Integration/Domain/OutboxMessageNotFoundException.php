<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use RuntimeException;

class OutboxMessageNotFoundException extends RuntimeException
{
}
