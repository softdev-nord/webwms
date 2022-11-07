<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Command\Helper;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper as BaseQuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Command\Helper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        QuestionHelper
 */
class QuestionHelper extends BaseQuestionHelper
{
    public function writeGeneratorSummary(OutputInterface $output, $errors)
    {
        if (!$errors) {
            $this->writeSection($output, 'Everything is OK! Now get to work :).');
        } else {
            $this->writeSection($output, [
                'The command was not able to configure everything automatically.',
                'You\'ll need to make the following changes manually.',
            ], 'error');

            $output->writeln($errors);
        }
    }

    public function getRunner(OutputInterface $output, &$errors)
    {
        $runner = function ($err, $outputResponseStatus = true) use ($output, &$errors) {
            if ($err) {
                if ($outputResponseStatus) {
                    $output->writeln('<fg=red>FAILED</>');
                }
                $errors = array_merge($errors, $err);
            } elseif ($outputResponseStatus) {
                $output->writeln('<info>OK</info>');
            }
        };

        return $runner;
    }

    public function getQuestion($question, $default, $sep = ':'): string
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }

    public function writeSection(OutputInterface $output, $text, $style = 'bg=blue;fg=white')
    {
        $text = 'webWMS bundle generator';

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');
        $output->writeln([
            '',
            $formatter->formatBlock($text, $style, true),
            '',
        ]);
    }
}
