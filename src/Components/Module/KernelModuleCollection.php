<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        KernelModuleCollection
 */
class KernelModuleCollection
{
    /**
     * @param Module[] $modules
     */
    public function __construct(
        private array $modules = []
    ) {
    }

    public function add(Module $module): void
    {
        /** @var string|false $class */
        $class = $module::class;

        if ($class === false) {
            return;
        }

        if ($this->has($class)) {
            return;
        }

        $this->modules[$class] = $module;
    }

    /**
     * @param array<mixed> $modules
     */
    public function addList(array $modules): void
    {
        foreach ($modules as $module) {
            $this->add($module);
        }
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->modules);
    }

    public function get(string $name): ?Module
    {
        return $this->has($name) ? $this->modules[$name] : null;
    }

    /**
     * @return Module[]
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * @return Module[]
     */
    public function getActives(): array
    {
        if (!$this->modules) {
            return [];
        }

        return array_filter($this->modules, static fn (Module $module) => $module->isActive());
    }

    public function filter(\Closure $closure): KernelModuleCollection
    {
        return new self(array_filter($this->modules, $closure));
    }
}
