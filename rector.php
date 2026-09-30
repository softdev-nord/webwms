<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassConst\RemoveFinalFromConstRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\NarrowObjectReturnTypeRector;
use WebWMS\Rector\RemoveFinalFromClassRector;

return RectorConfig::configure()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
    )
    ->withAttributesSets(
        symfony: true,
        doctrine: true,
        phpunit: true
    )
    ->withPhpSets(
        php84: true
    )
    ->withRules([
            PreferPHPUnitSelfCallRector::class,
            RemoveFinalFromClassRector::class,
        ]
    )
    ->withPHPStanConfigs(
        [
            __DIR__ . '/phpstan.neon'
        ]
    )
    ->withImportNames(
        removeUnusedImports: true
    )
    ->withPaths([
        __DIR__ . '/src/',
        __DIR__ . '/tests/'
    ])
    ->withSkip([
        NarrowObjectReturnTypeRector::class,
        __DIR__ . '/src/Kernel.php',
    ]);

