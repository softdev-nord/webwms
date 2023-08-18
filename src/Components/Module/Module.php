<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Symfony\Component\HttpKernel\Bundle\Bundle as SymfonyModule;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Module
 */
abstract class Module extends SymfonyModule
{
    final public function __construct(
        private readonly bool $active,
        private string $basePath,
        ?string $projectDir = null
    ) {
        if ($projectDir && mb_strpos($this->basePath, '/') !== 0) {
            $this->basePath = rtrim($projectDir, '/') . '/' . $this->basePath;
        }

        $this->path = $this->computeModuleClassPath();
    }

    public function getMigrationNamespace(): string
    {
        return $this->getNamespace() . '\Migration';
    }

    public function getMigrationPath(): string
    {
        $migrationSuffix = str_replace(
            $this->getNamespace(),
            '',
            $this->getMigrationNamespace()
        );

        return $this->getPath() . str_replace('\\', '/', $migrationSuffix);
    }

    final public function getContainerPrefix(): string
    {
        return (new CamelCaseToSnakeCaseNameConverter())->normalize($this->getName());
    }

    final public function isActive(): bool
    {
        return $this->active;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    private function computeModuleClassPath(): string
    {
        $moduleClassPath = $this->getPath();
        $modulePath = realpath($this->basePath);

        if ($modulePath !== false && mb_strpos($moduleClassPath, $modulePath) === 0) {
            $relativeModuleClassPath = mb_substr($moduleClassPath, mb_strlen($modulePath));

            return $this->basePath . $relativeModuleClassPath;
        }

        return $this->getPath();
    }
}
