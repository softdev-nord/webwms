<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('test_generator');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('openai_api_key')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
