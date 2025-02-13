<?php

declare(strict_types=1);

namespace WebWMS\Event\Stock;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Event\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockEvents'
)]
class StockEvents
{
    public const KARTON = 'Durchlaufregal';

    public const PALETTE = 'Pal Regal';

    public const BLOCK = 'Block-Lager';
}
