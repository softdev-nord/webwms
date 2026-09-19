<?php

declare(strict_types=1);

namespace WebWMS\Integration\Domain;

use WebWMS\Integration\Application\PublishedIntegrationMessage;

interface ErpStatusTransport
{
    public function deliver(ErpConnection $connection, PublishedIntegrationMessage $message): void;
}
