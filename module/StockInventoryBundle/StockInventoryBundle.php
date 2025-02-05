<?php

declare(strict_types=1);

namespace WebWMS\Bundles\StockInventoryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Bundles\StockInventoryBundle',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInventoryBundle'
)]
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
