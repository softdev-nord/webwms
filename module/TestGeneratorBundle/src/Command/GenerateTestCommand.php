<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\Command;

use SoftDevNord\TestGenerator\Helper\FileLocator;
use SoftDevNord\TestGenerator\Service\FileService;
use SoftDevNord\TestGenerator\Service\OpenAiClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function sprintf;

#[AsCommand(
    name: 'sdn:generate-test',
    description: 'Generiert PHPUnit-Tests'
)]
class GenerateTestCommand extends Command
{
    private const array DESCRIPTIONS = [
        'TestCase' => 'basic PHPUnit tests',
        'KernelTestCase' => 'basic tests that have access to Symfony services',
        'WebTestCase' => 'to run browser-like scenarios, but that don\'t execute JavaScript code',
        'ApiTestCase' => 'to run API-oriented scenarios',
        'PantherTestCase' => 'to run e2e scenarios, using a real-browser or HTTP client and a real web server',
    ];
    private const array DOCS = [
        'TestCase' => 'https://symfony.com/doc/current/testing.html#unit-tests',
        'KernelTestCase' => 'https://symfony.com/doc/current/testing/database.html#functional-testing-of-a-doctrine-repository',
        'WebTestCase' => 'https://symfony.com/doc/current/testing.html#functional-tests',
        'ApiTestCase' => 'https://api-platform.com/docs/distribution/testing/',
        'PantherTestCase' => 'https://github.com/symfony/panther#testing-usage',
    ];

    public function __construct(
        private readonly OpenAiClient $openAiClient,
        private readonly FileLocator $fileLocator,
        private readonly FileService $fileService
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $typesDesc = [];
        foreach (self::DESCRIPTIONS as $type => $desc) {
            $typesDesc[] = sprintf('<fg=yellow>%s</> (%s)', $type, $desc);
        }

        $this
            ->addArgument(
                'caseType',
                InputArgument::REQUIRED,
                'PHPUnit test case type: '.implode(', ', $typesDesc)
            )
            ->addArgument(
                'className',
                InputArgument::REQUIRED,
                'Full class name incl. namespace (e.g. <fg=yellow>App\\Service\\ExampleService</>)'
            )
            ->setHelp(
                "This command generates a PHPUnit test.\n\n".
                "Usage:\n".
                "  php bin/console generate:test TestCase App\Service\ExampleService\n"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $testCaseType = $input->getArgument('caseType');
        $fullClassName = $input->getArgument('className');

        $namespaceParts = explode('\\', $fullClassName);
        $className = array_pop($namespaceParts);
        $namespace = implode('\\', $namespaceParts);

        // Extract namespace and class name
        if (!str_contains($fullClassName, '\\')) {
            $io->error(
                'Please enter a complete namespace (e.g. App\\Service\\ExampleService).'
            );

            return Command::FAILURE;
        }

        // Get class file
        $classFile = $this->fileLocator->getFilePath($fullClassName);

        if (!$classFile || !file_exists($classFile)) {
            $io->error(
                sprintf(
                    'File "%s" not found.',
                    $classFile
                )
            );

            return Command::FAILURE;
        }

        $classCode = file_get_contents($classFile);

        $io->info(
            sprintf(
                'Generate PHPUnit test for %s...',
                $fullClassName
            )
        );

        // Generate PHPUnit-Test
        $generatedTest = $this->openAiClient->generatePhpUnitTest(
            $className,
            $namespace,
            $testCaseType,
            $classCode
        );

        // Save file
        $testFile = $this->fileService->saveFile($namespace, $className, $generatedTest);

        $io->text([
            'Next: Open your new test class and start customizing it.',
            sprintf(
                'Find the documentation at <fg=yellow>%s</>',
                self::DOCS[$testCaseType]
            ),
        ]);

        $io->info(
            sprintf(
                'Test generated: %s',
                $testFile
            )
        );

        return Command::SUCCESS;
    }
}
