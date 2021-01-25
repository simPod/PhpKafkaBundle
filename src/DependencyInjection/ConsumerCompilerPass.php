<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ConsumerCompilerPass implements CompilerPassInterface
{
    public const TAG_NAME_CONSUMER = KafkaExtension::ALIAS . '.consumer';

    public function process(ContainerBuilder $containerBuilder): void
    {
        $consumerServices = [];

        $definition = $containerBuilder->setDefinition(
            ConsumerBag::class,
            new Definition(
                ConsumerBag::class,
                [$consumerServices]
            )
        );

        /** @var array<string, array<string, mixed>> $taggedServiceIds */
        $taggedServiceIds = $containerBuilder->findTaggedServiceIds(self::TAG_NAME_CONSUMER);
        foreach ($taggedServiceIds as $id => $tags) {
            $definition->addMethodCall('addConsumer', [new Reference($id)]);
        }
    }
}
