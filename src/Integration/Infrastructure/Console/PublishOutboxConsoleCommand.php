<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Console;

use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WebWMS\Integration\Application\OutboxPublisher;

#[AsCommand(name: 'webwms:outbox:publish', description: 'Publish due integration outbox messages to the async queue')]
final class PublishOutboxConsoleCommand extends Command
{
    public function __construct(
        private readonly OutboxPublisher $publisher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum messages per run', '100');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = filter_var($input->getOption('limit'), FILTER_VALIDATE_INT);
        if (!is_int($limit) || $limit < 1 || $limit > 1000) {
            throw new \InvalidArgumentException('The limit must be between 1 and 1000.');
        }

        $report = $this->publisher->publishDue($limit, new DateTimeImmutable());
        $output->writeln(sprintf(
            'claimed=%d published=%d retry_scheduled=%d dead_lettered=%d',
            $report->claimed,
            $report->published,
            $report->retryScheduled,
            $report->deadLettered,
        ));

        return $report->deadLettered === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
