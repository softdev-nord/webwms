<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use WebWMS\Components\Module\Context\UninstallContext;
use WebWMS\Entity\Module;

class ModulePreUninstallEvent extends ModuleLifecycleEvent
{
    public function __construct(
        Module $module,
        private readonly UninstallContext $context
    ) {
        parent::__construct($module);
    }

    public function getContext(): UninstallContext
    {
        return $this->context;
    }
}
