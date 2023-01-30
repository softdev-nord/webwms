<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DevelopmentHelperBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class MakeModule extends Command
{
    private string $moduleFolderDir;

    public function __construct(string $kernelRootDir)
    {
        parent::__construct();
        $this->moduleFolderDir = $kernelRootDir.'/modules/';
    }

    public static $defaultName = 'webwms:make:module';

    public function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Module Name')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Start namespace of the module');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        $modulePath = $this->moduleFolderDir.'/'.$input->getArgument('name');
        if ($filesystem->exists($modulePath)) {
            throw new \RuntimeException(sprintf('The module with name "%s" already exists', $input->getArgument('name')));
        }

        $namespace = $input->getOption('namespace') ?? $input->getArgument('name');

        $filesystem->mkdir([
            $modulePath,
            $modulePath.'',
            $modulePath.'/Resources',
            $modulePath.'/Resources/config',
        ]);

        $symfonyStyle = new SymfonyStyle($input, $output);

        $this->makeComposerJson($filesystem, $modulePath, $input->getArgument('name'), $namespace, $symfonyStyle);
        $this->makeBootstrap($filesystem, $modulePath, $input->getArgument('name'), $namespace);
        $this->makeChangelogFiles($filesystem, $modulePath);
        $this->makeDefaultServicesYaml($filesystem, $modulePath);

        return 0;
    }

    private function makeComposerJson(Filesystem $filesystem, string $modulePath, string $moduleName, string $namespace, SymfonyStyle $symfonyStyle): void
    {
        $composerJson = [
            'name' => $symfonyStyle->ask('Composer Package name (webwms/package-name)', 'webwms/package-name'),
            'version' => '1.0.0',
            'description' => $symfonyStyle->ask('Package description', ''),
            'type' => 'symfony-bundle',
            'license' => $symfonyStyle->ask('Package license', 'MIT'),
            'autoload' => [
                'psr-4' => [
                    $namespace.'\\' => '',
                ],
            ],
            'extra' => [
                'webwms-module-class' => $namespace.'\\'.$moduleName,
                'label' => [
                    'de-DE' => $symfonyStyle->ask('Module Label [DE]') ?? $moduleName,
                    'en-GB' => $symfonyStyle->ask('Module Label [EN]') ?? $moduleName,
                ],
                'description' => [
                    'de-DE' => $symfonyStyle->ask('Module Description [DE]') ?? $moduleName,
                    'en-GB' => $symfonyStyle->ask('Module Description [EN]') ?? $moduleName,
                ],
                'manufacturerLink' => [
                    'de-DE' => $manufacturerLink = $symfonyStyle->ask('Module Manufacturer Link') ?? 'https://example.com',
                    'en-GB' => $manufacturerLink,
                ],
                'supportLink' => [
                    'de-DE' => $supportLink = $symfonyStyle->ask('Module Support Link') ?? 'https://example.com/support',
                    'en-GB' => $supportLink,
                ],
            ],
        ];

        $filesystem->dumpFile($modulePath.'/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function makeBootstrap(Filesystem $filesystem, string $modulePath, string $moduleName, string $namespace): void
    {
        $tpl = <<<EOL
<?php 

declare(strict_types=1);

namespace #namespace#;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class #class# extends Bundle
{
}
EOL;

        $filesystem->dumpFile($modulePath.''.$moduleName.'.php', str_replace(['#namespace#', '#class#'], [$namespace, $moduleName], $tpl));
    }

    private function makeChangelogFiles(Filesystem $filesystem, string $modulePath): void
    {
        $deChangelog = <<<EOL
# 1.0.0

- Initiale Veröffentlichung
EOL;

        $enChangelog = <<<EOL
# 1.0.0

- Initial publication
EOL;
        $filesystem->dumpFile($modulePath.'/CHANGELOG_de-DE.md', $deChangelog);
        $filesystem->dumpFile($modulePath.'/CHANGELOG_en-GB.md', $enChangelog);
    }

    private function makeDefaultServicesYaml(Filesystem $filesystem, string $modulePath): void
    {
        $yaml = <<<YAML
services:
{% block services %}
    # default configuration for services in *this* file
    _defaults:
        # automatically injects dependencies in your services
        autowire: true
        # automatically registers your services as commands, event subscribers, etc.
        autoconfigure: true
        # this means you cannot fetch services directly from the container via container->get()
        # if you need to do this, you can override this setting on individual services
        public: false

    # controllers are imported separately to make sure they're public
    # and have a tag that allows actions to type-hint services
    WebWMS\Bundles\{{ bundle }}\Controller\:
        resource: '../../Controller'
        public: true
        tags: ['controller.service_arguments']

# add more services, or override services that need manual wiring
#    WebWMS\Bundles\{{ bundle }}\ExampleClass:
#        arguments:
#            - "@service_id"
#            - "plain_value"
#            - "%parameter%"
{% endblock services %}
YAML;

        $filesystem->dumpFile($modulePath.'/Resources/config/services.yaml', $yaml);
    }
}
