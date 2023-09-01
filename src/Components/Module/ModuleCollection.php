<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use WebWMS\Components\Collection;
use WebWMS\Entity\Module;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ModuleCollection
 */
class ModuleCollection extends Collection
{
    protected function getExpectedClass(): string
    {
        return Module::class;
    }
}
