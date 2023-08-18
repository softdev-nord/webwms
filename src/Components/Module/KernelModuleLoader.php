<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Composer\Autoload\ClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WebWMS\Exception\KernelModuleLoaderException;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        KernelModuleLoader
 */
abstract class KernelModuleLoader extends Bundle
{
    /**
     * @var array<int, mixed>
     */
    protected array $moduleInfos = [];

    private readonly KernelModuleCollection $moduleInstances;

    private readonly string $moduleDir;

    private bool $initialized = false;

    public function __construct(
        private readonly ClassLoader $classLoader,
        ?string $moduleDir = null
    ) {
        $this->moduleDir = $moduleDir ?? 'modules';
        $this->moduleInstances = new KernelModuleCollection();
    }

    final public function getModuleDir(string $projectDir): string
    {
        // absolute path
        if (mb_strpos($this->moduleDir, '/') === 0) {
            return $this->moduleDir;
        }

        return $projectDir . '/' . $this->moduleDir;
    }

    /**
     * @return array<int, mixed>
     * Basic information required for instantiating the modules
     */
    final public function getModuleInfos(): array
    {
        return $this->moduleInfos;
    }

    /**
     * @final
     * Instances of the module classes
     */
    public function getModuleInstances(): KernelModuleCollection
    {
        return $this->moduleInstances;
    }

    /**
     * @param array<int, string> $loadedModules
     *
     * @return \Traversable<Module>
     */
    final public function getModules(array $loadedModules = []): iterable
    {
        if (!$this->initialized) {
            return;
        }

        foreach ($this->moduleInstances->getActives() as $module) {
            $loadedModules[] = $module->getName();
        }

        if (!\in_array($this->getName(), $loadedModules, true)) {
            yield $this;
        }
    }

    final public function initializeModules(string $projectDir): void
    {
        if ($this->initialized) {
            return;
        }

        $this->loadModuleInfos();
        if (!$this->moduleInfos) {
            $this->initialized = true;

            return;
        }

        $this->registerModuleNamespaces($projectDir);
        $this->instantiateModules($projectDir);

        $this->initialized = true;
    }

    final public function build(ContainerBuilder $container): void
    {
        if (!$this->initialized) {
            return;
        }

        parent::build($container);

        /*
         * Register every module in the di container, enable autowire and set public
         */
        foreach ($this->moduleInstances->getActives() as $module) {
            $class = $module::class;

            $definition = new Definition();
            if ($container->hasDefinition($class)) {
                $definition = $container->getDefinition($class);
            }

            $definition->setFactory([new Reference(self::class), 'getModuleInstance']);
            $definition->addArgument($class);

            $definition->setAutowired(true);
            $definition->setPublic(true);

            $container->setDefinition($class, $definition);
        }
    }

    final public function getModuleInstance(string $class): ?Module
    {
        $module = $this->moduleInstances->get($class);
        if (!$module || !$module->isActive()) {
            return null;
        }

        return $module;
    }

    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    abstract protected function loadModuleInfos(): void;

    private function registerModuleNamespaces(string $projectDir): void
    {
        foreach ($this->moduleInfos as $module) {
            \assert(\is_string($module['baseClass']));
            $moduleName = $module['name'] ?? $module['baseClass'];

            // modules managed by composer are already in the classMap
            if ($module['managedByComposer']) {
                continue;
            }

            if (!isset($module['autoload'])) {
                $reason = sprintf(
                    'Unable to register module "%s" in autoload. Required property `autoload` missing.',
                    $module['baseClass']
                );

                throw new KernelModuleLoaderException($moduleName, $reason);
            }

            $psr4 = $module['autoload']['psr-4'] ?? [];
            $psr0 = $module['autoload']['psr-0'] ?? [];

            if (!isset($psr4) && !isset($psr0)) {
                $reason = sprintf(
                    'Unable to register module "%s" in autoload. Required property `psr-4` or `psr-0` missing in property autoload.',
                    $module['baseClass']
                );

                throw new KernelModuleLoaderException($moduleName, $reason);
            }

            foreach ($psr4 as $namespace => $paths) {
                if (\is_string($paths)) {
                    $paths = [$paths];
                }
                $mappedPaths = $this->mapPsrPaths($moduleName, $paths, $projectDir, $module['path']);
                $this->classLoader->addPsr4($namespace, $mappedPaths);
                if ($this->classLoader->isClassMapAuthoritative()) {
                    $this->classLoader->setClassMapAuthoritative(false);
                }
            }

            foreach ($psr0 as $namespace => $paths) {
                if (\is_string($paths)) {
                    $paths = [$paths];
                }
                $mappedPaths = $this->mapPsrPaths($moduleName, $paths, $projectDir, $module['path']);

                $this->classLoader->add($namespace, $mappedPaths);
                if ($this->classLoader->isClassMapAuthoritative()) {
                    $this->classLoader->setClassMapAuthoritative(false);
                }
            }
        }
    }

    /**
     * @param array<string> $psr
     *
     * @throws KernelModuleLoaderException
     *
     * @return list<string>
     */
    private function mapPsrPaths(string $module, array $psr, string $projectDir, string $moduleRootPath): array
    {
        $mappedPaths = [];

        $absoluteModuleRootPath = $this->getAbsoluteModuleRootPath($projectDir, $moduleRootPath);

        if (mb_strpos($absoluteModuleRootPath, $projectDir) !== 0) {
            throw new KernelModuleLoaderException(
                $module,
                sprintf('Module dir %s needs to be a sub-directory of the project dir %s', $moduleRootPath, $projectDir)
            );
        }

        foreach ($psr as $path) {
            $mappedPaths[] = $absoluteModuleRootPath . '/' . $path;
        }

        return $mappedPaths;
    }

    private function getAbsoluteModuleRootPath(string $projectDir, string $moduleRootPath): string
    {
        // is relative path
        if (mb_strpos($moduleRootPath, '/') !== 0) {
            $moduleRootPath = $projectDir . '/' . $moduleRootPath;
        }

        return $moduleRootPath;
    }

    /**
     * @throws KernelModuleLoaderException
     */
    private function instantiateModules(string $projectDir): void
    {
        foreach ($this->moduleInfos as $moduleData) {
            $className = $moduleData['baseClass'];

            $moduleClassFilePath = $this->classLoader->findFile($className);
            if (!class_exists($className) || !$moduleClassFilePath || !file_exists($moduleClassFilePath)) {
                continue;
            }

            /** @var \WebWMS\Components\Module\Module $module */
            $module = new $className((bool) $moduleData['active'], $moduleData['path'], $projectDir);

            if (!$module instanceof Module) {
                $reason = sprintf('Module class "%s" must extend "%s"', $module::class, Module::class);

                throw new KernelModuleLoaderException($moduleData['name'], $reason);
            }

            $this->moduleInstances->add($module);
        }
    }

    /**
     * @param Module[] $modules
     *
     * @return array<Module[]>
     */
    private function splitModulesIntoPreAndPost(array $modules): array
    {
        $pre = [];
        $post = [];

        foreach ($modules as $index => $module) {
            if (\is_int($index) && $index < 0) {
                $pre[$index] = $module;
            } else {
                $post[$index] = $module;
            }
        }

        \ksort($pre);
        \ksort($post);

        return [$pre, $post];
    }
}
