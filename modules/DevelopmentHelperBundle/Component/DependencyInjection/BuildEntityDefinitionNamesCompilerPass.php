<?php

namespace WebWMS\Bundles\DevelopmentHelperBundle\Component\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuildEntityDefinitionNamesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $names = [];

        foreach ($container->findTaggedServiceIds('entity.definition') as $id => $options) {
            if ($container->hasAlias($id)) {
                continue;
            }

            $names[] = $container->getDefinition($id)->getClass();
        }

        $container->setParameter('development_helper.names', array_unique($names));
    }
}
