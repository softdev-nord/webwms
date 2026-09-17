<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\DependencyInjection;

use Composer\Util\Platform;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TestGeneratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Load your service definitions
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(
                __DIR__ . '/../../config'
            )
        );
        $loader->load('services.yaml');
    }
}
