<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    private const string DEFAULT_BOOTSTRAP_SERVER_LIST = '127.0.0.1:9092';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(KafkaExtension::ALIAS);

        $this->configureConnection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function configureConnection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()
            ->scalarNode('bootstrap_servers')
            ->defaultValue(self::DEFAULT_BOOTSTRAP_SERVER_LIST);

        $rootNode->children()
            ->scalarNode('authentication')
            ->defaultValue(null);

        $clientNode = new ArrayNodeDefinition('client');
        $clientNode
            ->children()
            ->scalarNode('id');

        $rootNode
            ->append($clientNode);
    }
}
