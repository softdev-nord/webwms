<?php

declare(strict_types=1);

namespace WebWMS\Administration\Infrastructure\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Administration\Application\Demo\DemoBootstrapService;

#[AsCommand(name: 'webwms:v3:demo-bootstrap', description: 'Creates an idempotent V3 demo tenant and stock data.')]
final class BootstrapDemoConsoleCommand extends Command
{
    public function __construct(private readonly DemoBootstrapService $bootstrap)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('password', null, InputOption::VALUE_REQUIRED, 'Password for a newly created demo user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $password = $input->getOption('password');
        $result = $this->bootstrap->bootstrap(is_string($password) ? $password : null);

        $io->success($result->created ? 'V3 demo data was created.' : 'V3 demo data already exists.');
        $io->definitionList(
            ['Tenant ID' => $result->tenantId],
            ['E-mail' => $result->email],
        );
        if ($result->generatedPassword !== null) {
            $io->warning('Save this generated password now; it will not be shown again.');
            $io->writeln(sprintf('Password: %s', $result->generatedPassword));
        }

        return Command::SUCCESS;
    }
}
