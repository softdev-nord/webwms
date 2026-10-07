<?php

declare(strict_types=1);

namespace WebWMS\Documentation\UI\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Documentation\Application\Screenshot\CaptureHandbookScreenshots;

#[AsCommand(name: 'webwms:handbook:capture-screenshots', description: 'Validate or capture reproducible screenshots for the user handbook')]
final class CaptureHandbookScreenshotsCommand extends Command
{
    public function __construct(private readonly CaptureHandbookScreenshots $capture)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('locale', null, InputOption::VALUE_REQUIRED, 'de, en or all', 'de')
            ->addOption('view', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Scenario or documentation view key')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Handbook category')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite existing screenshots')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Validate without starting a browser')
            ->addOption('headed', null, InputOption::VALUE_NONE, 'Run the browser visibly');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locale = (string) $input->getOption('locale');
        if (!in_array($locale, ['de', 'en', 'all'], true)) {
            $io->error('The locale must be de, en or all.');

            return Command::INVALID;
        }
        $views = $input->getOption('view');
        $views = is_array($views) ? array_values(array_filter($views, is_string(...))) : [];
        $category = $input->getOption('category');
        $category = is_string($category) && $category !== '' ? $category : null;
        $rows = [];
        $failed = false;
        try {
            foreach ($locale === 'all' ? ['de', 'en'] : [$locale] as $language) {
                foreach ($this->capture->capture(
                    $views,
                    $category,
                    $language,
                    (bool) $input->getOption('force'),
                    (bool) $input->getOption('dry-run'),
                    (bool) $input->getOption('headed'),
                ) as $result) {
                    $rows[] = [$language, $result->scenario, $result->status, $result->target, $result->message ?? ''];
                    $failed = $failed || $result->status === 'failed';
                }
            }
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
        $io->table(['Locale', 'Scenario', 'Status', 'Target', 'Message'], $rows);
        if ($failed) {
            $io->error('Screenshot capture finished with errors.');

            return Command::FAILURE;
        }

        $io->success('Handbook screenshot processing completed.');

        return Command::SUCCESS;
    }
}
