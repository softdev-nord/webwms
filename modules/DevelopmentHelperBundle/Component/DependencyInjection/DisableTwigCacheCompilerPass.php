<?php

namespace WebWMS\Bundles\DevelopmentHelperBundle\Component\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DisableTwigCacheCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('twig.debug', true);
    }
}
