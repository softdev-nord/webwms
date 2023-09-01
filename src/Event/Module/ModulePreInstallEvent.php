<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use WebWMS\Components\Module\Context\InstallContext;
use WebWMS\Entity\Module;

class ModulePreInstallEvent extends ModuleLifecycleEvent
{
    public function __construct(
        Module $module,
        private readonly InstallContext $context
    ) {
        parent::__construct($module);
    }

    public function getContext(): InstallContext
    {
        return $this->context;
    }
}
