<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command\Lifecycle;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Annotation\Context;
use WebWMS\Exception\ModuleNotInstalledException;

#[AsCommand(
    name: 'module:install',
    description: 'Installs a module',
)]
class ModuleInstallCommand extends AbstractModuleLifecycleCommand
{
    private const LIFECYCLE_METHOD = 'install';

    protected function configure(): void
    {
        $this->configureCommand(self::LIFECYCLE_METHOD);
        $this->addOption('activate', 'a', InputOption::VALUE_NONE, 'Activate modules after installation.')
            ->addOption('reinstall', null, InputOption::VALUE_NONE, 'Reinstall the modules');
    }

    /**
     * {@inheritdoc}
     *
     * @throws ModuleNotInstalledException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $context = Context::createDefaultContext();
        $modules = $this->prepareExecution(self::LIFECYCLE_METHOD, $io, $input);

        if ($modules === null) {
            return self::SUCCESS;
        }

        $activateModules = $input->getOption('activate');

        $installedModuleCount = 0;
        foreach ($modules as $module) {
            if ($input->getOption('reinstall') && $module->getInstalledAt()) {
                $this->moduleLifecycleService->uninstallModule($module, $context);
            }

            if ($activateModules && $module->getInstalledAt() && $module->getActive() === false) {
                $io->note(sprintf('Module "%s" is already installed. Activating.', $module->getName()));
                $this->moduleLifecycleService->activateModule($module, $context);

                continue;
            }

            if ($module->getInstalledAt()) {
                $io->note(sprintf('Module "%s" is already installed. Skipping.', $module->getName()));

                continue;
            }

            $activationSuffix = '';
            $message = 'Module "%s" has been installed%s successfully.';

            $this->moduleLifecycleService->installModule($module, $context);
            ++$installedModuleCount;

            if ($activateModules) {
                if ($input->getOption('refresh')) {
                    $io->note('Can not refresh and activate in same request.');
                } else {
                    $this->moduleLifecycleService->activateModule($module, $context);
                    $activationSuffix = ' and activated';
                }
            }

            $io->text(sprintf($message, $module->getName(), $activationSuffix));
        }

        if ($installedModuleCount !== 0) {
            $io->success(sprintf('Installed %d module(s).', $installedModuleCount));
        }

        if ($activateModules) {
            $this->handleClearCacheOption($input, $io, 'activating');
        }

        return self::SUCCESS;
    }
}
