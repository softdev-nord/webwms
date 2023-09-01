<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use WebWMS\Components\Module\Context\ActivateContext;
use WebWMS\Entity\Module;

class ModulePreActivateEvent extends ModuleLifecycleEvent
{
    public function __construct(
        Module $module,
        private readonly ActivateContext $context
    ) {
        parent::__construct($module);
    }

    public function getContext(): ActivateContext
    {
        return $this->context;
    }
}
