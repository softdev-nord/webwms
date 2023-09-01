<?php

declare(strict_types=1);

namespace WebWMS\Components\Module\Command\Lifecycle;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use WebWMS\Components\Module\ModuleCollection;
use WebWMS\Components\Module\ModuleLifecycleService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use WebWMS\Entity\Module;
use WebWMS\Repository\ModuleRepository;

abstract class AbstractModuleLifecycleCommand extends Command
{
    public function __construct(
        private readonly ModuleRepository $moduleRepository,
        protected ModuleLifecycleService $moduleLifecycleService
    ) {
        parent::__construct();
    }

    protected function configureCommand(string $lifecycleMethod): void
    {
        $this
            ->setDescription(sprintf('%ss given modules', ucfirst($lifecycleMethod)))
            ->addArgument(
                'modules',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'List of modules'
            );
    }

    /**
     * @throws \Throwable
     */
    protected function prepareExecution(
        string $lifecycleMethod,
        SymfonyStyle $io,
        InputInterface $input
    ): ?ModuleCollection {
        $io->title('Shopware Module Lifecycle Service');

        if ($input->getOption('refresh')) {
            $io->note('Refreshing module list');
            $this->refreshModules();
        }

        $modules = $this->parseModuleArgument($input->getArgument('modules'), $lifecycleMethod, $io);

        if ($modules->count() === 0) {
            $io->warning('No modules found');
            $io->text('Try the module:refresh command first, run composer update for changes in the modules composer.json, or change your search term');

            return $modules;
        }

        $io->text(sprintf('%s %d module(s):', ucfirst($lifecycleMethod), \count($modules)));
        $io->listing($this->formatModuleList($modules));

        return $modules;
    }

    /**
     * @throws \Throwable
     */
    protected function refreshModules(): void
    {
        $input = new StringInput('module:refresh -s');
        /** @var Application $application */
        $application = $this->getApplication();
        $application->doRun($input, new NullOutput());
    }

    /**
     * @param array<mixed> $arguments
     */
    private function parseModuleArgument(
        array $arguments,
        string $lifecycleMethod,
        SymfonyStyle $io,
    ): ModuleCollection
    {
        $pluginCollection = $this->moduleRepository->getEntities();

        if ($pluginCollection->count() <= 1) {
            return $pluginCollection;
        }

        $choiceAbort = 'Cancel.';
        $choiceSelect = sprintf('Select one Module to %s.', $lifecycleMethod);

        $choice = $io->askQuestion(
            new ChoiceQuestion(
                sprintf(
                    '%d modules were found. How do you want to continue?',
                    $pluginCollection->count()
                ),
                [
                    sprintf('%s all of them.', $lifecycleMethod),
                    $choiceSelect,
                    $choiceAbort,
                ]
            )
        );

        if ($choice === $choiceSelect) {
            $id = $io->askQuestion(
                new Question(
                    sprintf(
                        'Which module do you want to %s?',
                        $lifecycleMethod
                    ),
                    $pluginCollection->map(fn (Module $plugin) => $plugin->getName())
                )
            );

            return new ModuleCollection([$pluginCollection->get($id)]);
        }

        return $pluginCollection;
    }

    /**
     * @return array<string>
     */
    private function formatModuleList(ModuleCollection $modules): array
    {
        $moduleList = [];
        foreach ($modules as $module) {
            $moduleList[] = sprintf('%s (v%s)', $module->getLabel(), $module->getVersion());
        }

        return $moduleList;
    }
}
