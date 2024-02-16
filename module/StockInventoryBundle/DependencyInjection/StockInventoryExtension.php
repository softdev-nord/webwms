<?php

declare(strict_types=1);

namespace WebWMS\Bundles\StockInventoryBundle\DependencyInjection;

use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @package:    WebWMS\Bundles\StockInventoryBundle\DependencyInjection
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInventoryExtension
 */
class StockInventoryExtension extends Extension
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');
    }
}
