<?php

declare(strict_types=1);

namespace WebWMS\Components;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Components',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'Constants'
)]
class Constants
{
    public function setTheme(): string
    {
        return 'vertical'; // Theme config
    }
}
