<?php

declare(strict_types=1);

namespace WebWMS\Bundles\LogViewerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package:    WebWMS\Bundles\LogViewerBundle\DependencyInjection
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2022, SoftDev Nord
 * Class        LogViewerExtension
 */
class LogViewerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}
