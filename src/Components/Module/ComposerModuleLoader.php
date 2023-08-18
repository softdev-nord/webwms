<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Composer\InstalledVersions;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ComposerModuleLoader
 */
class ComposerModuleLoader
{
    /**
     * @var array<int, mixed>
     */
    protected array $moduleInfos = [];

    private readonly string $moduleDir;

    public function __construct(
        ?string $moduleDir = null
    ) {
        $this->moduleDir = $moduleDir ?? 'modules';
    }

    protected function loadModuleInfos(): void
    {
        if (
            !method_exists(InstalledVersions::class, 'getInstalledPackagesByType')
            || !method_exists(InstalledVersions::class, 'getInstallPath')
        ) {
            throw new \RuntimeException('FallbackModuleLoader does only work with Composer 2.1 or higher');
        }

        $composerModules = InstalledVersions::getInstalledPackagesByType(ModuleFinder::COMPOSER_TYPE);

        $this->moduleInfos = [];

        foreach ($composerModules as $composerName) {
            $path = InstalledVersions::getInstallPath($composerName);
            $composerJsonPath = $path . '/composer.json';

            if (!\file_exists($composerJsonPath)) {
                continue;
            }

            $composerJsonContent = \file_get_contents($composerJsonPath);
            \assert(\is_string($composerJsonContent));

            $composerJson = \json_decode($composerJsonContent, true, 512, \JSON_THROW_ON_ERROR);
            \assert(\is_array($composerJson));
            $moduleClass = $composerJson['extra']['webwms-module-class'] ?? '';

            if (\defined('\STDERR') && ($moduleClass === '' || !\class_exists($moduleClass))) {
                \fwrite(\STDERR, \sprintf('Skipped package %s due invalid "webwms-module-class" config', $composerName) . \PHP_EOL);

                continue;
            }

            $nameParts = \explode('\\', (string) $moduleClass);

            $this->moduleInfos[] = [
                'name' => \end($nameParts),
                'baseClass' => $moduleClass,
                'active' => true,
                'path' => $path,
                'version' => InstalledVersions::getPrettyVersion($composerName),
                'autoload' => $composerJson['autoload'] ?? [],
                'managedByComposer' => true,
                'composerName' => $composerName,
            ];
        }
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
}
