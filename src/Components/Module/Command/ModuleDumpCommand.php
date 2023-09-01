<?php

declare(strict_types=1);

namespace WebWMS\Components\Module\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebWMS\Components\Module\ModuleConfigGeneratorInterface;

#[AsCommand(name: 'bundle:dump', description: 'Dumps the bundle configuration for a module')]
class ModuleDumpCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ModuleConfigGeneratorInterface $bundleDumper,
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addArgument('dumpFilePath', InputArgument::OPTIONAL, 'By default to var/modules.json', 'var/modules.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->bundleDumper->getConfig();

        \file_put_contents(
            $this->projectDir . '/' . $input->getArgument('dumpFilePath'),
            \json_encode($config, \JSON_PRETTY_PRINT)
        );

        return self::SUCCESS;
    }
}
