<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DevelopmentHelperBundle\Component\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WebWMS\Bundles\DevelopmentHelperBundle\Component\Profiler\TwigDataCollector;
use WebWMS\Bundles\DevelopmentHelperBundle\Component\Profiler\TwigDecorator;

class CustomProfilerExtensions implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('data_collector.twig')) {
            $definition = $container->getDefinition('data_collector.twig');
            $definition->setClass(TwigDataCollector::class);

            $parameter = $container->getParameter('data_collector.templates');
            $parameter['data_collector.twig'][1] = '@DevelopmentHelperBundle/Collector/twig.html.twig';
            $container->setParameter('data_collector.templates', $parameter);
        }

        if ($container->hasDefinition('twig')) {
            $container->getDefinition('twig')->setClass(TwigDecorator::class);
        }
    }
}
