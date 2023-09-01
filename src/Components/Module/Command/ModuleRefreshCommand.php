<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Service\ModuleService;

#[AsCommand(
    name: 'module:refresh',
    description: 'Refreshes the module list',
)]
class ModuleRefreshCommand extends Command
{
    public function __construct(
        private readonly ModuleService $moduleService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('skipModuleList', 's', InputOption::VALUE_NONE, 'Don\'t display module list after refresh');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Shopware Module Service');

        $composerInput = clone $input;
        $composerInput->setInteractive(false);
        $helperSet = $this->getHelperSet();
        \assert($helperSet instanceof HelperSet);
        $io->success('Module list refreshed');

        $skipModuleList = $input->getOption('skipModuleList');
        if ($skipModuleList) {
            return self::SUCCESS;
        }

        $listInput = new StringInput('module:list');

        /** @var Application $application */
        $application = $this->getApplication();
        $application->doRun($listInput, $output);

        return self::SUCCESS;
    }
}
