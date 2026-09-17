<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\Helper;

class FileLocator
{
    public function getFilePath(string $fullClassName): string
    {
        $namespaceParts = explode('\\', $fullClassName);
        $className = array_pop($namespaceParts);
        $namespace = implode('\\', $namespaceParts);
        $loader = require getcwd() . '/vendor/autoload.php';

        return $loader->findFile($fullClassName);
    }
}
