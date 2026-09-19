<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

interface OutboxRepository
{
    public function append(IntegrationStatusEvent $event): void;

    public function acknowledge(OutboxAcknowledgement $acknowledgement): void;
}
