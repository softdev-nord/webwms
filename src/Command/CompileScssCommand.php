<?php

declare(strict_types=1);

namespace WebWMS\Command;

use WebWMS\Service\ScssCompilerService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'webwms:build-frontend',
    description: 'Compiles all SCSS files from assets/scss and assets/styles to public/assets/css and public/assets/styles.'
)]
class CompileScssCommand extends Command
{
    public function __construct(
        private readonly ScssCompilerService $scssCompiler
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->scssCompiler->copyAssets();
        $io->success('Assets wurden erfolgreich kopiert!');

        $scssFiles = ['app.scss', 'bootstrap.scss', 'custom.scss', 'icons.scss', 'sidebar.scss'];

        $io->info('Kompiliere SCSS-Dateien');

        try {
            $this->scssCompiler->compileMultiple($scssFiles);
            $io->success('SCSS-Dateien wurden erfolgreich kompiliert!');
        } catch (Exception $e) {
            $io->error('Fehler: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
