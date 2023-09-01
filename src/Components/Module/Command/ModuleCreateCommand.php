<?php declare(strict_types=1);

namespace WebWMS\Components\Module\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'module:create',
    description: 'Creates a new module',
)]
class ModuleCreateCommand extends Command
{
    private string $composerJsonTemplate = <<<EOL
{
  "name": "webwms/module-skeleton",
  "description": "Skeleton module",
  "type": "webwms-module",
  "license": "MIT",
  "autoload": {
    "psr-4": {
      "#namespace#\\\\": ""
    }
  },
  "extra": {
    "webwms-module-class": "#namespace#\\\\#class#",
    "label": {
      "de-DE": "Skeleton Modul",
      "en-GB": "Skeleton module"
    }
  }
}

EOL;

    private string $bootstrapTemplate = <<<EOL
<?php
 
declare(strict_types=1);

namespace #namespace#;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class #class# extends Bundle
{
}
EOL;

    private string $servicesYamlTemplate = <<<EOL
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

    #namespace#\:
        resource: '../../*'
        exclude: '../../{Controller,DependencyInjection,Tests,#class#.php}'

    #namespace#\Controller\:
        resource: '../../Controller/*'
        tags: ['controller.service_arguments']
EOL;

    private string $configYamlTemplate = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/shopware/platform/trunk/src/Core/System/SystemConfig/Schema/config.xsd">
    <card>
        <title>#moduleName# Settings</title>
        <title lang="de-DE">#moduleName# Einstellungen</title>

        <input-field type="bool">
            <name>active</name>
            <label>Active</label>
            <label lang="de-DE">Aktiviert</label>
        </input-field>
    </card>
</config>
EOL;

    /**
     * @internal
     */
    public function __construct(
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('create-config', 'c', InputOption::VALUE_NONE, 'Create config.yml');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $question = new Question('Please enter a module name: ');
            $name = $this->getHelper('question')->ask($input, $output, $question);
        }

        $name = ucfirst((string) $name);

        $directory = $this->projectDir . '/module/' . $name;

        if (file_exists($directory)) {
            throw new \RuntimeException(sprintf('Module directory %s already exists', $directory));
        }

        mkdir($directory . '/src/Resources/config/', 0777, true);

        $composerFile = $directory . '/composer.json';
        $bootstrapFile = $directory . '/' . $name . '.php';
        $servicesYamlFile = $directory . '/Resources/config/services.yml';

        $composer = str_replace(
            ['#namespace#', '#class#'],
            [$name, $name],
            $this->composerJsonTemplate
        );

        $bootstrap = str_replace(
            ['#namespace#', '#class#'],
            [$name, $name],
            $this->bootstrapTemplate
        );

        file_put_contents($composerFile, $composer);
        file_put_contents($bootstrapFile, $bootstrap);
        file_put_contents($servicesYamlFile, $this->servicesYamlTemplate);

        if ($input->getOption('create-config')) {
            $configYamlFile = $directory . '/Resources/config/config.yml';
            $configXml = str_replace(
                ['moduleName'],
                [$name],
                $this->configYamlTemplate
            );

            file_put_contents($configYamlFile, $configXml);
        }

        return self::SUCCESS;
    }
}
