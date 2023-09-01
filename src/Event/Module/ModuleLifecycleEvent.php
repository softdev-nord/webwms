<?php

declare(strict_types=1);

namespace WebWMS\Event\Module;

use Symfony\Contracts\EventDispatcher\Event;
use WebWMS\Entity\Module;

abstract class ModuleLifecycleEvent extends Event
{
    public function __construct(private readonly Module $module)
    {
    }

    public function getModule(): Module
    {
        return $this->module;
    }
}
