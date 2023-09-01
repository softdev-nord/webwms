<?php declare(strict_types=1);

namespace WebWMS\Components\Module;

use WebWMS\Entity\Module as ModuleEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WebWMS\Components\Module\Context\ActivateContext;
use WebWMS\Components\Module\Context\DeactivateContext;
use WebWMS\Components\Module\Context\InstallContext;
use WebWMS\Components\Module\Context\UninstallContext;
use WebWMS\Components\Module\Context\UpdateContext;
use WebWMS\Event\Module\ModulePostActivateEvent;
use WebWMS\Event\Module\ModulePostDeactivateEvent;
use WebWMS\Event\Module\ModulePostDeactivationFailedEvent;
use WebWMS\Event\Module\ModulePostInstallEvent;
use WebWMS\Event\Module\ModulePostUninstallEvent;
use WebWMS\Event\Module\ModulePostUpdateEvent;
use WebWMS\Event\Module\ModulePreActivateEvent;
use WebWMS\Event\Module\ModulePreDeactivateEvent;
use WebWMS\Event\Module\ModulePreInstallEvent;
use WebWMS\Event\Module\ModulePreUninstallEvent;
use WebWMS\Event\Module\ModulePreUpdateEvent;
use WebWMS\Exception\ModuleBaseClassNotFoundException;
use WebWMS\Exception\ModuleNotActivatedException;
use WebWMS\Exception\ModuleNotInstalledException;
use WebWMS\Repository\ModuleRepository;
use WebWMS\Service\Configuration\ConfigurationService;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\ModuleService;

/**
 * @internal
 */
class ModuleLifecycleService
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private readonly KernelModuleCollection $moduleCollection,
        private ContainerInterface $container,
        private readonly string $appVersionNumber,
        private readonly ConfigurationService $configurationService,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function installModule(ModuleEntity $module): InstallContext
    {
        $moduleData = [];
        $moduleBaseClass = $this->getModuleBaseClass($module->getBaseClass());
        $appVersionNumber = $module->getVersion();

        $installContext = new InstallContext(
            $moduleBaseClass,
            $this->appVersionNumber
        );

        if ($module->getInstalledAt()) {
            return $installContext;
        }

        $moduleData['id'] = $module->getId();

        $this->eventDispatcher->dispatch(new ModulePreInstallEvent($module, $installContext));

        $this->configurationService->saveModuleConfiguration($moduleBaseClass);

        $moduleBaseClass->install($installContext);

        $installDate = $this->dateTimeService->createDateTime();
        $moduleData['installedAt'] = $installDate;
        $module->setInstalledAt($installDate);

        $this->updateModuleData($moduleData);

        $moduleBaseClass->postInstall($installContext);

        $this->eventDispatcher->dispatch(new ModulePostInstallEvent($module, $installContext));

        return $installContext;
    }

    /**
     * @throws ModuleNotInstalledException|\Throwable
     */
    public function uninstallModule(
        ModuleEntity $module
    ): UninstallContext {
        if ($module->getInstalledAt() === null) {
            throw new ModuleNotInstalledException($module->getName());
        }

        if ($module->getActive()) {
            $this->deactivateModule($module);
        }

        $moduleBaseClassString = $module->getBaseClass();
        $moduleBaseClass = $this->getModuleBaseClass($moduleBaseClassString);

        $uninstallContext = new UninstallContext(
            $moduleBaseClass,
            $this->appVersionNumber
        );

        $this->eventDispatcher->dispatch(new ModulePreUninstallEvent($module, $uninstallContext));

        $moduleBaseClass->uninstall($uninstallContext);

        $this->configurationService->deleteModuleConfiguration($moduleBaseClass);

        $moduleId = $module->getId();
        $this->updateModuleData(
            [
                'id' => $moduleId,
                'active' => false,
                'installedAt' => null,
            ],
        );
        $module->setActive(false);
        $module->setInstalledAt(null);

        $this->eventDispatcher->dispatch(new ModulePostUninstallEvent($module, $uninstallContext));

        return $uninstallContext;
    }

    /**
     * @throws ModuleNotInstalledException
     */
    public function activateModule(ModuleEntity $module, bool $reactivate = false): ActivateContext
    {
        if ($module->getInstalledAt() === null) {
            throw new ModuleNotInstalledException($module->getName());
        }

        $moduleBaseClassString = $module->getBaseClass();
        $moduleBaseClass = $this->getModuleBaseClass($moduleBaseClassString);

        $activateContext = new ActivateContext(
            $moduleBaseClass,
            $this->appVersionNumber
        );

        if ($reactivate === false && $module->getActive()) {
            return $activateContext;
        }

        $this->eventDispatcher->dispatch(new ModulePreActivateEvent($module, $activateContext));

        $module->setActive(true);

        $this->rebuildContainerWithNewModuleState($module);

        $moduleBaseClass = $this->getModuleInstance($moduleBaseClassString);
        $activateContext = new ActivateContext(
            $moduleBaseClass,
            $this->appVersionNumber
        );

        $moduleBaseClass->activate($activateContext);

        $this->updateModuleData(
            [
                'id' => $module->getId(),
                'active' => true,
            ]
        );

        $this->eventDispatcher->dispatch(new ModulePostActivateEvent($module, $activateContext));

        return $activateContext;
    }

    /**
     * @throws ModuleNotInstalledException
     * @throws ModuleNotActivatedException|\Throwable
     */
    public function deactivateModule(ModuleEntity $module): DeactivateContext
    {
        if ($module->getInstalledAt() === null) {
            throw new ModuleNotInstalledException($module->getName());
        }

        if ($module->getActive() === false) {
            throw new ModuleNotActivatedException($module->getName());
        }

        $moduleBaseClassString = $module->getBaseClass();
        $moduleBaseClass = $this->getModuleInstance($moduleBaseClassString);

        $deactivateContext = new DeactivateContext(
            $moduleBaseClass,
            $this->appVersionNumber
        );

        $this->eventDispatcher->dispatch(new ModulePreDeactivateEvent($module, $deactivateContext));

        try {
            $moduleBaseClass->deactivate($deactivateContext);

            $module->setActive(false);

            $this->updateModuleData(
                [
                    'id' => $module->getId(),
                    'active' => false,
                ],
            );
        } catch (\Throwable $exception) {
            $activateContext = new ActivateContext(
                $moduleBaseClass,
                $this->appVersionNumber
            );

            $this->eventDispatcher->dispatch(
                new ModulePostDeactivationFailedEvent(
                    $module,
                    $activateContext,
                    $exception
                )
            );

            throw $exception;
        }

        $this->eventDispatcher->dispatch(new ModulePostDeactivateEvent($module, $deactivateContext));

        return $deactivateContext;
    }

    private function getModuleBaseClass(string $moduleBaseClassString): Module
    {
        $baseClass = $this->moduleCollection->get($moduleBaseClassString);

        if ($baseClass === null) {
            throw new ModuleBaseClassNotFoundException($moduleBaseClassString);
        }

        // set container because the plugin has not been initialized yet and therefore has no container set
        $baseClass->setContainer($this->container);

        return $baseClass;
    }

    /**
     * @param array<string, mixed|null> $moduleData
     */
    private function updateModuleData(array $moduleData): void
    {
        $this->pluginRepo->update([$moduleData]);
    }

    private function rebuildContainerWithNewModuleState(ModuleEntity $module): void
    {
        $kernel = $this->container->get('kernel');

        $moduleDir = $kernel->getContainer()->getParameter('kernel.plugin_dir');
        if (!\is_string($moduleDir)) {
            throw new \RuntimeException('Container parameter "kernel.plugin_dir" needs to be a string');
        }

        $moduleLoader = $this->container->get(KernelModuleLoader::class);

        $modules = $moduleLoader->getModuleInfos();
        foreach ($modules as $i => $moduleData) {
            if ($moduleData['baseClass'] === $module->getBaseClass()) {
                $modules[$i]['active'] = $module->getActive();
            }
        }

        try {
            $newContainer = $kernel->getContainer();
        } catch (\LogicException) {
            // If symfony throws an exception when calling getContainer on a not booted kernel and catch it here
            throw new \RuntimeException('Failed to reboot the kernel');
        }

        $this->container = $newContainer;
        $this->eventDispatcher = $newContainer->get('event_dispatcher');
    }

    private function getModuleInstance(string $moduleBaseClassString): Module
    {
        if ($this->container->has($moduleBaseClassString)) {
            $containerModule = $this->container->get($moduleBaseClassString);
            if (!$containerModule instanceof Module) {
                throw new \RuntimeException($moduleBaseClassString . ' in the container should be an instance of ' . Module::class);
            }

            return $containerModule;
        }

        return $this->getModuleBaseClass($moduleBaseClassString);
    }

    public function updateModule(ModuleEntity $plugin): UpdateContext
    {
        if ($plugin->getInstalledAt() === null) {
            throw new ModuleNotInstalledException($plugin->getName());
        }

        $pluginBaseClassString = $plugin->getBaseClass();
        $pluginBaseClass = $this->getModuleBaseClass($pluginBaseClassString);

        $updateContext = new UpdateContext(
            $pluginBaseClass,
            $this->appVersionNumber
        );

        $this->eventDispatcher->dispatch(new ModulePreUpdateEvent($plugin, $updateContext));

        $this->configurationService->saveModuleConfiguration($pluginBaseClass);

        try {
            $pluginBaseClass->update($updateContext);
        } catch (\Throwable $updateException) {
            if ($plugin->getActive()) {
                try {
                    $this->deactivateModule($plugin);
                } catch (\Throwable) {
                    $this->updateModuleData(
                        [
                            'id' => $plugin->getId(),
                            'active' => false,
                        ],
                    );
                }
            }

            throw $updateException;
        }

        $updateDate = $this->dateTimeService->createDateTime();
        $this->updateModuleData(
            [
                'id' => $plugin->getId(),
                'upgradeVersion' => null,
                'upgradedAt' => $updateDate,
            ],
        );
        $plugin->setVersion($plugin->getVersion());
        $plugin->setUpgradedAt($updateDate);

        $pluginBaseClass->postUpdate($updateContext);

        $this->eventDispatcher->dispatch(new ModulePostUpdateEvent($plugin, $updateContext));

        return $updateContext;
    }
}
