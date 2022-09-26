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

    /** @param mixed[] $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setParameter(self::ALIAS, $this->processConfiguration(new Configuration(), $configs));

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources'),
        );

        $loader->load('config.yaml');
    }

    public function getAlias(): string
    {
        return self::ALIAS;
    }
}
