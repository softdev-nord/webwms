<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use WebWMS\Components\Module\Context\DeactivateContext;
use WebWMS\Entity\Module;

class ModulePostDeactivateEvent extends ModuleLifecycleEvent
{
    public function __construct(
        Module $module,
        private readonly DeactivateContext $context
    ) {
        parent::__construct($module);
    }

    public function getContext(): DeactivateContext
    {
        return $this->context;
    }
}
