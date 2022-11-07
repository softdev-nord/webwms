<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Generator;

use Symfony\Component\Console\Output\ConsoleOutput;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Generator
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Generator
 */
class Generator
{
    private array $skeletonDirs;
    private static $output;

    /**
     * Sets an array of directories to look for templates.
     *
     * The directories must be sorted from the most specific to the most
     * directory.
     *
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs(array $skeletonDirs)
    {
        $this->skeletonDirs = is_array($skeletonDirs) ? $skeletonDirs : [$skeletonDirs];
    }

    protected function render($template, $parameters): string
    {
        $twig = $this->getTwigEnvironment();

        return $twig->render($template, $parameters);
    }

    /**
     * Gets the twig environment that will render skeletons.
     */
    protected function getTwigEnvironment(): Environment
    {
        return new Environment(new FilesystemLoader($this->skeletonDirs), [
            'debug' => true,
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => false,
        ]);
    }

    protected function renderFile($template, $target, $parameters): bool|int
    {
        self::mkdir(dirname($target));

        return self::dump($target, $this->render($template, $parameters));
    }

    /**
     * @internal
     */
    public static function mkdir($dir, $mode = 0777, $recursive = true)
    {
        if (!is_dir($dir)) {
            mkdir($dir, $mode, $recursive);
            self::writeln(sprintf('  <fg=green>created</> %s', self::relativizePath($dir)));
        }
    }

    /**
     * @internal
     */
    public static function dump($filename, $content): bool|int
    {
        if (file_exists($filename)) {
            self::writeln(sprintf('  <fg=yellow>updated</> %s', self::relativizePath($filename)));
        } else {
            self::writeln(sprintf('  <fg=green>created</> %s', self::relativizePath($filename)));
        }

        return file_put_contents($filename, $content);
    }

    private static function writeln($message)
    {
        if (null === self::$output) {
            self::$output = new ConsoleOutput();
        }

        self::$output->writeln($message);
    }

    private static function relativizePath($absolutePath): array|string
    {
        $relativePath = str_replace(getcwd(), '.', $absolutePath);

        return is_dir($absolutePath) ? rtrim($relativePath, '/').'/' : $relativePath;
    }
}
