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
            ->setHelp(<<<'HELP'
                The <info>%command.name%</info> command validates or creates the reproducible
                screenshots used by the integrated user handbook.

                Capture all German screenshots:

                  <info>php %command.full_name%</info>

                Capture the German and English screenshots and overwrite existing files:

                  <info>php %command.full_name% --locale=all --force</info>

                Capture one scenario or documentation view:

                  <info>php %command.full_name% --view=product_form --force</info>

                Multiple views can be selected by repeating the option:

                  <info>php %command.full_name% --view=product_overview --view=product_detail</info>

                Capture every scenario in one handbook category:

                  <info>php %command.full_name% --category=warehouse --force</info>

                Validate the configuration, demo references and target paths without
                starting a browser or writing screenshots:

                  <info>php %command.full_name% --dry-run --locale=all</info>

                Open the browser visibly while debugging a scenario:

                  <info>php %command.full_name% --view=warehouse_occupancy_block --headed --force</info>

                The browser login requires <comment>HANDBOOK_SCREENSHOT_EMAIL</comment> and
                <comment>HANDBOOK_SCREENSHOT_PASSWORD</comment>. Set
                <comment>HANDBOOK_SCREENSHOT_BASE_URL</comment> when the application is not
                available at http://localhost.
                HELP)
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
