<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use WebWMS\Integration\Domain\WcsConnection;

interface WcsCommandTransport
{
    public function deliver(WcsConnection $connection, PublishedIntegrationMessage $message): void;
}
