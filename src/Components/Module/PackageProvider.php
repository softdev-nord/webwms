<?php

declare(strict_types=1);

namespace WebWMS\Components\Module;

use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;

/**
 * @package:    WebWMS\Components\Module
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        PackageProvider
 */
class PackageProvider
{
    public function getModuleComposerPackage(string $modulePath, IOInterface $composerIO): CompletePackageInterface
    {
        return ComposerFactory::createComposer($modulePath, $composerIO)->getPackage();
    }
}
