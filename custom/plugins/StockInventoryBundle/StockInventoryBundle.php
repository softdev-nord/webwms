<?php

declare(strict_types=1);

namespace WebWMS\Bundles\StockInventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class StockInventoryBundle extends Bundle
{
    public function getNiceName(): string
    {
        return 'webWMS Inventur';
    }

    public function getDescription(): string
    {
        return 'Bundle für das webWMS Inventur Handling';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
