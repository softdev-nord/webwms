<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(PhpVersion::PHP_81);
    $rectorConfig->removeUnusedImports();
    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');

    $rectorConfig->paths([
        __DIR__ . '/bundles',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->sets([
//        SetList::DEAD_CODE,
//        SetList::CODE_QUALITY,
//        SetList::CODING_STYLE,
//        SetList::PHP_81,
//        SetList::PSR_4,
//        SetList::TYPE_DECLARATION,
//        SetList::INSTANCEOF,
//        SetList::EARLY_RETURN,
//        SetList::PRIVATIZATION,
//        SetList::NAMING,
//        SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION,
//        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        SymfonySetList::SYMFONY_62
    ]);
};
