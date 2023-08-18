<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ModuleConfigLocator
 *
 * Findet Configs aus Modulen, wenn Resources/config existiert.
 *
 * Versucht zuerst, <Name>_<Umgebung>.<Suffix> zu finden und greift auf <Name>.<Suffix> zurück,
 * wenn die umgebungsspezifische Suche nichts gefunden hat. Es werden alle bekannten Suffixe durchsucht,
 * also z.B. wenn eine config.yaml und eine config.php existieren, werden beide verwendet.
 *
 * Beispiel: Suche nach config wird versuchen,
 * die folgenden Dateien aus jedem Module zu finden (es werden alle gefundenen Dateien zurückgegeben):
 *
 *  - Resources/config/config_dev.php
 *  - Resources/config/config_dev.yaml
 *  - Resources/config/config_dev.yml
 *  - Resources/config/config_dev.xml
 *
 * Wenn die vorangegangene Suche keine Ergebnisse lieferte, wird auf die vorherige Suche zurückgegriffen:
 *
 *  - Resources/config/config.php
 *  - Resources/config/config.yaml
 *  - Resources/config/config.yml
 *  - Resources/config/config.xml
 */
class ModuleConfigLocator
{
    public function __construct(
        private readonly KernelInterface $kernel
    ) {
    }

    /**
     * Suche nach Konfigurationsdateien mit dem angegebenen Namen (z.B. config)
     *
     * @param string $name
     *
     * @return array<mixed>
     */
    public function locate(string $name): array
    {
        $result = [];
        foreach ($this->kernel->getBundles() as $module) {
            $modulePath = $module->getPath();
            if (!is_dir($dir = $modulePath . '/Resources/config') && !is_dir($dir = $modulePath . '/config')) {
                continue;
            }

            // Zuerst versuchen, eine umgebungsspezifische Datei zu finden,
            // auf eine generische Datei zurückgreifen, wenn keine gefunden wird (z. B. config_dev.yaml > config.yaml)
            $finder = $this->buildContainerConfigFinder($name, $dir, true);
            if (!$finder->hasResults()) {
                $finder = $this->buildContainerConfigFinder($name, $dir, false);
            }

            foreach ($finder as $file) {
                $result[] = $file->getRealPath();
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @param string $directory
     * @param bool $includeEnvironment
     *
     * @return Finder
     */
    private function buildContainerConfigFinder(string $name, string $directory, bool $includeEnvironment = false): Finder
    {
        if ($includeEnvironment) {
            $name .= '_' . $this->kernel->getEnvironment();
        }

        $finder = new Finder();
        $finder->in($directory);

        foreach (['php', 'yml', 'yaml', 'xml'] as $extension) {
            $finder->name(sprintf('%s.%s', $name, $extension));
        }

        return $finder;
    }
}
