<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        BundleGeneratorBundle
 */
class BundleGeneratorBundle extends Bundle
{
    public function getNiceName(): string
    {
        return 'webWMS Bundle Generator';
    }

    public function getDescription(): string
    {
        return 'Bundle für die Erstellung von webWMS Bundles';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
