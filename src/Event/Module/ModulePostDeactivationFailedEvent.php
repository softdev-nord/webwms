<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use WebWMS\Components\Module\Context\ActivateContext;
use WebWMS\Entity\Module;

class ModulePostDeactivationFailedEvent extends ModuleLifecycleEvent
{
    public function __construct(
        Module $module,
        private readonly ActivateContext $context,
        private readonly ?\Throwable $exception = null
    ) {
        parent::__construct($module);
    }

    public function getContext(): ActivateContext
    {
        return $this->context;
    }

    public function getException(): ?\Throwable
    {
        return $this->exception;
    }
}
