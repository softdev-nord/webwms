<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

interface ModuleConfigGeneratorInterface
{
    /**
     * Returns the bundle config for the webpack plugin injector
     */
    public function getConfig(): array;
}
