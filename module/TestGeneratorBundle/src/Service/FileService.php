<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\Service;

use Symfony\Component\Filesystem\Filesystem;

class FileService
{
    public function getFilePath(string $fullClassName): string
    {
        $loader = require getcwd() . '/vendor/autoload.php';

        return $loader->findFile($fullClassName);
    }

    public function saveFile(
        string $namespace,
        string $className,
        string $generatedTest
    ): string {
        $testNamespace = str_replace('WebWMS', 'WebWMS\Tests', $namespace);
        $testDir = sprintf(
            '%s/tests/%s',
            getcwd(),
            str_replace('\\', '/', $testNamespace
            )
        );

        $testFile = sprintf(
            '%s/%sTest.php',
            $testDir,
            $className
        );

        $filesystem = new Filesystem();
        $filesystem->mkdir($testDir);
        $filesystem->dumpFile($testFile, $generatedTest);

        return $testFile;
    }
}
