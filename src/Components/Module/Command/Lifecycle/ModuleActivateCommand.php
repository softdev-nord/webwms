<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command\Lifecycle;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Exception\ModuleNotInstalledException;

#[AsCommand(
    name: 'module:activate',
    description: 'Activate a module',
)]
class ModuleActivateCommand extends AbstractModuleLifecycleCommand
{
    private const LIFECYCLE_METHOD = 'activate';

    protected function configure(): void
    {
        $this->configureCommand(self::LIFECYCLE_METHOD);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ModuleNotInstalledException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $modules = $this->prepareExecution(self::LIFECYCLE_METHOD, $io, $input);

        if ($modules === null) {
            return self::SUCCESS;
        }

        $activatedModuleCount = 0;
        foreach ($modules as $module) {
            if ($module->getInstalledAt() === null) {
                $io->note(sprintf('Module "%s" must be installed. Skipping.', $module->getName()));

                continue;
            }

            if ($module->getActive()) {
                $io->note(sprintf('Module "%s" is already active. Skipping.', $module->getName()));

                continue;
            }

            $this->moduleLifecycleService->activateModule($module);
            ++$activatedModuleCount;

            $io->text(sprintf('Module "%s" has been activated successfully.', $module->getName()));
        }

        if ($activatedModuleCount !== 0) {
            $io->success(sprintf('Activated %d module(s).', $activatedModuleCount));
        }

        return self::SUCCESS;
    }
}
