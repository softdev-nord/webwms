<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use WebWMS\Components\Module\Context\UpdateContext;
use WebWMS\Entity\Module;

#[Package('core')]
class ModulePreUpdateEvent extends ModuleLifecycleEvent
{
    public function __construct(
        Module $module,
        private readonly UpdateContext $context
    ) {
        parent::__construct($module);
    }

    public function getContext(): UpdateContext
    {
        return $this->context;
    }
}
