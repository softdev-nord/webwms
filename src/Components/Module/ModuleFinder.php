<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ModuleFinder
 */
class ModuleFinder
{
    final public const COMPOSER_TYPE = 'webwms-module';
    private const MODULE_CLASS_EXTRA_IDENTIFIER = 'webwms-module-class';

    public function __construct(
        private readonly PackageProvider $packageProvider
    ) {
    }

    /**
     * @return ModuleFromFileSystem[]
     * @throws \Exception
     */
    public function findModules(
        string $moduleDir,
        string $projectDir,
        IOInterface $composerIO
    ): array {
        $modules = $this->loadLocalModules($moduleDir, $composerIO);

        return $this->enrichWithVendorModules($modules, $projectDir, $composerIO);
    }

    /**
     * @return array<string, ModuleFromFileSystem>
     * @throws \Exception
     */
    private function loadLocalModules(string $moduleDir, IOInterface $composerIO): array
    {
        $modules = [];

        try {
            $filesystemModules = (new Finder())
                ->directories()
                ->depth(0)
                ->in($moduleDir)
                ->sortByName()
                ->getIterator();

            foreach ($filesystemModules as $filesystemModule) {
                $modulePath = $filesystemModule->getRealPath();
                $package = $this->packageProvider->getModuleComposerPackage($modulePath, $composerIO);

                $moduleName = $this->getModuleNameFromPackage($package);

                $modules[$moduleName] = (new ModuleFromFileSystem())->assign([
                    'baseClass' => $moduleName,
                    'path' => $filesystemModule->getPathname(),
                    'managedByComposer' => false,
                    'composerPackage' => $package,
                ]);
            }
        } catch (DirectoryNotFoundException) {
        }

        return $modules;
    }

    private function isWebWmsModuleType(CompletePackageInterface $package): bool
    {
        return $package->getType() === self::COMPOSER_TYPE;
    }

    private function isModuleComposerValid(CompletePackageInterface $package): bool
    {
        return isset($package->getExtra()[self::MODULE_CLASS_EXTRA_IDENTIFIER])
            && $package->getExtra()[self::MODULE_CLASS_EXTRA_IDENTIFIER] !== ''
            && !isset($package->getExtra()['label']);
    }

    private function getModuleNameFromPackage(CompletePackageInterface $modulePackage): string
    {
        return $modulePackage->getExtra()[self::MODULE_CLASS_EXTRA_IDENTIFIER];
    }

    /**
     * @param array<string, ModuleFromFileSystem> $modules
     *
     * @return array<string, ModuleFromFileSystem>
     */
    private function enrichWithVendorModules(
        array $modules,
        string $projectDir,
        IOInterface $composerIO
    ): array {
        $composer = ComposerFactory::createComposer($projectDir, $composerIO);

        /** @var CompletePackageInterface[] $composerPackages */
        $composerPackages = $composer
            ->getRepositoryManager()
            ->getLocalRepository()
            ->getPackages();

        foreach ($composerPackages as $composerPackage) {

            $modulePath = $this->getVendorModulePath($composerPackage, $composer);
            if (!$this->isModuleComposerValid($composerPackage)) {

                continue;
            }

            $moduleBaseClass = $this->getModuleNameFromPackage($composerPackage);

            $localModule = $modules[$moduleBaseClass] ?? null;

            $modules[$moduleBaseClass] = (new ModuleFromFileSystem())->assign([
                'baseClass' => $moduleBaseClass,
                // use local path if it is also installed as a local module,
                // to allow updates over the store for composer managed modules
                'path' => $localModule?->getPath() ?? $modulePath,
                'managedByComposer' => true,
                // use local composer package (if it exists) as composer caches the version info
                'composerPackage' => $localModule?->getComposerPackage() ?? $composerPackage,
            ]);
        }

        $root = $composer->getPackage();
        if ($this->isWebWmsModuleType($root) && $this->isModuleComposerValid($root)) {
            $moduleBaseClass = $this->getModuleNameFromPackage($root);
            $modules[$moduleBaseClass] = (new ModuleFromFileSystem())->assign([
                'baseClass' => $moduleBaseClass,
                'path' => '.',
                'managedByComposer' => true,
                'composerPackage' => $root,
            ]);
        }

        return $modules;
    }

    private function getVendorModulePath(CompletePackageInterface $modulePackage, Composer $composer): string
    {
        return $composer->getConfig()->get('vendor-dir') . '/' . $modulePackage->getPrettyName();
    }
}
