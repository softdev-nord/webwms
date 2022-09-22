<?php

declare(strict_types=1);

namespace WebWMS\Command;

use sixlive\DotenvEditor\DotenvEditor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'regenerate-app-secret',
    description: 'Regenerate a random value and update APP_SECRET',
)]
class RegenerateAppSecretCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('envfile', InputArgument::REQUIRED, 'env File {.env, .env.local}');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $envName = $input->getArgument('envfile');

        if ('.env' == $envName || '.env.local' == $envName) {
            $symfonyStyle->note(sprintf('You chose to update: %s', $envName));
            $secret = bin2hex(random_bytes(16));
            $filepath = realpath(dirname(__FILE__).'/../..').'/'.$envName;
            $symfonyStyle->note(sprintf('Editing file: %s', $filepath));

            $editor = new DotenvEditor();
            $editor->load($filepath);
            $editor->set('APP_SECRET', $secret);
            $editor->save();
            $symfonyStyle->success('New APP_SECRET was generated: '.$secret);

            return Command::SUCCESS;
        }
        $symfonyStyle->error('You did not provide a valid environment file to change');

        return Command::INVALID;
    }
}
