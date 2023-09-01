<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Components\Module\ModuleCollection;
use WebWMS\Repository\ModuleRepository;

#[AsCommand(
    name: 'module:list',
    description: 'Lists all modules',
)]
class ModuleListCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ModuleRepository $moduleRepository
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addOption('json', null, InputOption::VALUE_NONE, 'Return result as json of module entities')
            ->addOption('filter', 'f', InputOption::VALUE_REQUIRED, 'Filter the module list to a given term');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var ModuleCollection $modules */
        $modules = $this->moduleRepository->findAll();

        if ($input->getOption('json')) {
            $output->write(json_encode($modules, \JSON_THROW_ON_ERROR));

            return self::SUCCESS;
        }

        $moduleTable = [];
        $active = $installed = $upgradeable = 0;

        $io->title('WebWMS Module Service');

        foreach ($modules as $module) {
            $moduleActive = $module->getActive();
            $moduleInstalled = $module->getInstalledAt();
            $moduleUpgradeable = $module->getUpgradeVersion();

            $moduleTable[] = [
                $module->getName(),
                $module->getLabel(),
                $module->getVersion(),
                $moduleUpgradeable,
                $module->getAuthor(),
                $moduleInstalled ? 'Yes' : 'No',
                $moduleActive ? 'Yes' : 'No',
                $moduleUpgradeable ? 'Yes' : 'No',
            ];

            if ($moduleActive) {
                ++$active;
            }

            if ($moduleInstalled) {
                ++$installed;
            }

            if ($moduleUpgradeable) {
                ++$upgradeable;
            }
        }

        $io->table(
            ['Module', 'Label', 'Version', 'Upgrade version', 'Author', 'Installed', 'Active', 'Upgradeable'],
            $moduleTable
        );
        $io->text(
            sprintf(
                '%d modules, %d installed, %d active , %d upgradeable',
                \count($modules),
                $installed,
                $active,
                $upgradeable
            )
        );

        return self::SUCCESS;
    }
}
