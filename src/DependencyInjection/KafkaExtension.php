<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class KafkaExtension extends Extension
{
    public const ALIAS = 'kafka';

    /** @param mixed[] $config */
    public function load(array $config, ContainerBuilder $container) : void
    {
        $container->setParameter(self::ALIAS, $this->processConfiguration(new Configuration(), $config));

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources')
        );

        $loader->load('config.yaml');
    }

    public function getAlias() : string
    {
        return self::ALIAS;
    }
}
