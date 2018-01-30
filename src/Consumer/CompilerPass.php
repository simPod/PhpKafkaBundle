<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

use SimPod\Bundle\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CompilerPass implements CompilerPassInterface
{
    public const TAG_NAME_CONSUMER = KafkaExtension::ALIAS . '.consumer';

    public function process(ContainerBuilder $containerBuilder) : void
    {
        $consumerServices = [];

        $definition = $containerBuilder->setDefinition(
            Bag::class,
            new Definition(
                Bag::class,
                [$consumerServices]
            )
        );

        foreach ($containerBuilder->findTaggedServiceIds(self::TAG_NAME_CONSUMER) as $id => $tags) {
            $definition->addMethodCall('addConsumer', [new Reference($id)]);
        }
    }
}
