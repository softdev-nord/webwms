<?php

declare(strict_types=1);

namespace WebWMS\Bundles\BundleGeneratorBundle\Entity;

/**
 * @package:    WebWMS\Bundles\BundleGeneratorBundle\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Bundle
 */
class Bundle extends BaseBundle
{
    public function shouldGenerateDependencyInjectionDirectory(): bool
    {
        return true;
    }
}
