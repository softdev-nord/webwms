<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle as SymfonyModule;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use WebWMS\Components\Module\Context\ActivateContext;
use WebWMS\Components\Module\Context\DeactivateContext;
use WebWMS\Components\Module\Context\InstallContext;
use WebWMS\Components\Module\Context\UninstallContext;
use WebWMS\Components\Module\Context\UpdateContext;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Module
 */
abstract class Module extends SymfonyModule
{
    final public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private bool $active = false;

    final public function __construct(
        private string $basePath,
        ?string $projectDir = null
    ) {
        if ($projectDir && mb_strpos($this->basePath, '/') !== 0) {
            $this->basePath = rtrim($projectDir, '/') . '/' . $this->basePath;
        }

        $this->path = $this->computeModuleClassPath();
    }

    final public function isActive(): bool
    {
        return $this->active;
    }

    public function install(InstallContext $installContext): void
    {
    }

    public function postInstall(InstallContext $installContext): void
    {
    }

    public function update(UpdateContext $updateContext): void
    {
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
    }

    public function activate(ActivateContext $activateContext): void
    {
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
    }

    public function configureRoutes(RoutingConfigurator $routes, string $environment): void
    {
        $fileSystem = new Filesystem();
        $confDir = $this->getPath() . '/Resources/config';

        if ($fileSystem->exists($confDir)) {
            $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, 'glob');
            $routes->import($confDir . '/{routes}/' . $environment . '/**/*' . self::CONFIG_EXTS, 'glob');
            $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, 'glob');
            $routes->import($confDir . '/{routes}_' . $environment . self::CONFIG_EXTS, 'glob');
        }
    }

    public function configureRouteOverwrites(RoutingConfigurator $routes, string $environment): void
    {
        $fileSystem = new Filesystem();
        $confDir = $this->getPath() . '/Resources/config';

        if ($fileSystem->exists($confDir)) {
            $routes->import($confDir . '/{routes_overwrite}' . self::CONFIG_EXTS, 'glob');
        }
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
