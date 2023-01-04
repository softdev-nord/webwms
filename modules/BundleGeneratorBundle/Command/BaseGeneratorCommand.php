<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use WebWMS\Bundles\BundleGeneratorBundle\Command\Helper\QuestionHelper;
use WebWMS\Bundles\BundleGeneratorBundle\Generator\Generator;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Command
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        BaseGeneratorCommand
 */
abstract class BaseGeneratorCommand extends Command
{
    private ?Generator $generator = null;

    abstract protected function createGenerator();

    protected function getGenerator(BundleInterface $bundle = null): Generator
    {
        if (null === $this->generator) {
            $this->generator = $this->createGenerator();
            $this->generator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->generator;
    }

    protected $container;

    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();
            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null): array
    {
        $skeletonDirs = [];

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/GeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getProjectDir().'/Resources/GeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = __DIR__.'/../Resources/skeleton';
        $skeletonDirs[] = __DIR__.'/../Resources';

        return $skeletonDirs;
    }

    protected function getQuestionHelper(): Helper|HelperInterface|QuestionHelper
    {
        $question = $this->getHelperSet()->get('question');
        if (!$question || QuestionHelper::class !== get_class($question)) {
            $this->getHelperSet()->set($question = new QuestionHelper());
        }

        return $question;
    }

    /**
     * Tries to make a path relative to the project, which prints nicer.
     */
    protected function makePathRelative(string $absolutePath): string
    {
        $projectRootDir = dirname($this->getContainer()->getParameter('kernel.project_dir'));

        return str_replace($projectRootDir.'/', '', realpath($absolutePath) ?: $absolutePath);
    }
}
