<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\ErpConnection;

interface ErpStatusTransport
{
    public function deliver(ErpConnection $connection, PublishedIntegrationMessage $message): void;
}
