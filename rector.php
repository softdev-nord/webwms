<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use WebWMS\Helper\Development\RectorCustomRule\ConvertPhpDocToClassAttributeRector;

return RectorConfig::configure()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
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
        php83: true
    )
    ->withRules([
            PreferPHPUnitSelfCallRector::class
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
        __DIR__ . '/src/Kernel.php',
    ]);

