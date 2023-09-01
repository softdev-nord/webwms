<?php

declare(strict_types=1);

namespace WebWMS\Components\Module\Context;


use WebWMS\Components\Module\Module;

class InstallContext
{
    public function __construct(
        private readonly Module $module,
        private readonly string $appVersionNumber
    ) {
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function getAppVersionNumber(): string
    {
        return $this->appVersionNumber;
    }
}
