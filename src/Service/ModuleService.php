<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Symfony\Component\Filesystem\Filesystem;
use WebWMS\Components\Module\ModuleFinder;
use WebWMS\Entity\Module;
use WebWMS\Service\DataHandlers\Module\ModuleDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ModuleService
 */
class ModuleService
{
    final public const COMPOSER_AUTHOR_ROLE_MANUFACTURER = 'Manufacturer';

    public function __construct(
        private readonly ModuleFinder $moduleFinder,
        private readonly ModuleDataHandler $moduleDataHandler,
    ) {
    }

    /**
     * @throws \Exception
     *
     * @return array<mixed>
     */
    public function getModules(string $moduleDir, string $projectDir, IOInterface $composerIO): array
    {
        $moduleNames = [];

        $modulesFromFileSystem = $this->moduleFinder->findModules($moduleDir, $projectDir, $composerIO);
        $modulesFromDatabase = $this->moduleDataHandler->getAllModules();

        foreach ($modulesFromDatabase as $moduleFromDatabase) {
            $moduleNames[] = $moduleFromDatabase->getName();
        }

        $modules = [];
        foreach ($modulesFromFileSystem as $moduleFromFileSystem) {
            $baseClass = $moduleFromFileSystem->getBaseClass();
            $modulePath = $moduleFromFileSystem->getPath();
            $info = $moduleFromFileSystem->getComposerPackage();

            $extra = $info->getExtra();
            $license = $info->getLicense();

            $moduleData = [];

            if (in_array($moduleFromFileSystem->getName(), $moduleNames, true)) {
                $moduleData = [
                    'installedAt' => $this->getModuleByName($moduleFromFileSystem->getName())->getInstalledAt()->format('d.m.Y'),
                    'active' => $this->getModuleByName($moduleFromFileSystem->getName())->getActive(),
                    'description' => $this->getModuleByName($moduleFromFileSystem->getName())->getDescription(),
                ];
            }

            $moduleData['name'] = $moduleFromFileSystem->getName();
            $moduleData['baseClass'] = $baseClass;
            $moduleData['composerName'] = $info->getName();
            $moduleData['path'] = (new Filesystem())->makePathRelative($modulePath, $projectDir);
            $moduleData['author'] = $this->getAuthors($info);
            $moduleData['copyright'] = $extra['copyright'] ?? null;
            $moduleData['license'] = implode(', ', $license);
            $moduleData['version'] = $info->getVersion();
            $moduleData['autoload'] = $info->getAutoload();
            $moduleData['managedByComposer'] = $moduleFromFileSystem->getManagedByComposer();

            $modules[] = $moduleData;
        }

        return $modules;
    }

    private function getAuthors(CompletePackageInterface $info): string
    {
        $composerAuthors = $info->getAuthors();

        $manufacturerAuthors = array_filter(
            $composerAuthors,
            static fn (array $author): bool => ($author['role'] ?? '') === self::COMPOSER_AUTHOR_ROLE_MANUFACTURER
        );

        if (!$manufacturerAuthors) {
            $manufacturerAuthors = $composerAuthors;
        }

        $authorNames = array_column($manufacturerAuthors, 'name');

        return implode(', ', $authorNames);
    }

    public function getModuleById(int $moduleId): ?Module
    {
        return $this->moduleDataHandler->getModuleById($moduleId);
    }

    public function getModuleByName(string $moduleName): Module
    {
        return $this->moduleDataHandler->getModuleByName($moduleName);
    }

    /**
     * @return object[]|null
     */
    public function getAllModules(): ?array
    {
        return $this->moduleDataHandler->getAllModules();
    }
}
