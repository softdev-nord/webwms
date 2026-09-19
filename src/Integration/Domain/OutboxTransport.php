<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface OutboxTransport
{
    public function publish(OutboxMessage $message): void;
}
