<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command\Lifecycle;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Exception\ModuleNotInstalledException;

#[AsCommand(
    name: 'module:uninstall',
    description: 'Uninstall a module',
)]
class ModuleUninstallCommand extends AbstractModuleLifecycleCommand
{
    private const LIFECYCLE_METHOD = 'uninstall';

    protected function configure(): void
    {
        $this->configureCommand(self::LIFECYCLE_METHOD);
        $this->addOption('keep-user-data', null, InputOption::VALUE_NONE, 'Keep user data of the module');
    }

    /**
     * {@inheritdoc}
     *
     * @throws ModuleNotInstalledException
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $modules = $this->prepareExecution(self::LIFECYCLE_METHOD, $io, $input);

        if ($modules === null) {
            return self::SUCCESS;
        }

        $uninstalledModuleCount = 0;
        foreach ($modules as $module) {
            if ($module->getInstalledAt() === null) {
                $io->note(sprintf('Module "%s" is not installed. Skipping.', $module->getName()));

                continue;
            }

            $this->moduleLifecycleService->uninstallModule($module);
            ++$uninstalledModuleCount;

            $io->text(sprintf('Module "%s" has been uninstalled successfully.', $module->getName()));
        }

        if ($uninstalledModuleCount !== 0) {
            $io->success(sprintf('Uninstalled %d modules.', $uninstalledModuleCount));
        }

        return self::SUCCESS;
    }
}
