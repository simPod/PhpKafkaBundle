<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private const DEFAULT_BROKER = '127.0.0.1:9092';

    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root(KafkaExtension::ALIAS);

        $this->configureConnection($rootNode);

        return $treeBuilder;
    }

    private function configureConnection(ArrayNodeDefinition $rootNode) : void
    {
        $rootNode->children()
            ->scalarNode('broker_list')
            ->defaultValue(self::DEFAULT_BROKER);
    }
}
