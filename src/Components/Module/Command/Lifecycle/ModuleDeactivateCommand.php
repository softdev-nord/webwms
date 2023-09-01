<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command\Lifecycle;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Exception\ModuleNotActivatedException;
use WebWMS\Exception\ModuleNotInstalledException;

#[AsCommand(
    name: 'module:deactivate',
    description: 'Deactivates a module',
)]
class ModuleDeactivateCommand extends AbstractModuleLifecycleCommand
{
    private const LIFECYCLE_METHOD = 'deactivate';

    protected function configure(): void
    {
        $this->configureCommand(self::LIFECYCLE_METHOD);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ModuleNotInstalledException
     * @throws ModuleNotActivatedException
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $modules = $this->prepareExecution(self::LIFECYCLE_METHOD, $io, $input);

        if ($modules === null) {
            return self::SUCCESS;
        }

        $deactivatedModuleCount = 0;
        foreach ($modules as $module) {
            if ($module->getInstalledAt() === null) {
                $io->note(sprintf('Module "%s" must be installed. Skipping.', $module->getName()));

                continue;
            }

            if ($module->getActive() === false) {
                $io->note(sprintf('Module "%s" must be activated. Skipping.', $module->getName()));

                continue;
            }

            $this->moduleLifecycleService->deactivateModule($module);
            ++$deactivatedModuleCount;

            $io->text(sprintf('Module "%s" has been deactivated successfully.', $module->getName()));
        }

        if ($deactivatedModuleCount !== 0) {
            $io->success(sprintf('Deactivated %d module(s).', $deactivatedModuleCount));
        }

        return self::SUCCESS;
    }
}
