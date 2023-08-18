<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use WebWMS\Components\Environment\EnvironmentHelper;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ComposerFactory
 */
class ComposerFactory
{
    public static function createComposer(string $composerJsonDir, ?IOInterface $composerIO = null): Composer
    {
        if ($composerIO === null) {
            $composerIO = new NullIO();
        }

        $composerJsonPath = $composerJsonDir . '/composer.json';

        $json = json_decode((string) file_get_contents($composerJsonPath), true, \JSON_THROW_ON_ERROR);

        $previousRootVersion = EnvironmentHelper::hasVariable('COMPOSER_ROOT_VERSION') ? EnvironmentHelper::getVariable('COMPOSER_ROOT_VERSION') : null;

        $composer = (new Factory())->createComposer(
            $composerIO,
            $composerJsonPath,
            false,
            $composerJsonDir
        );

        if ($previousRootVersion === null) {
            unset($_SERVER['COMPOSER_ROOT_VERSION']);
        } else {
            $_SERVER['COMPOSER_ROOT_VERSION'] = $previousRootVersion;
        }

        return $composer;
    }
}
