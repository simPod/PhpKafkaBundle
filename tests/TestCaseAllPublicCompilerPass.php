<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function strpos;

final class TestCaseAllPublicCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (strpos($id, 'SimPod\KafkaBundle\Kafka') === false) {
                continue;
            }
            $definition->setPublic(true);
        }

        foreach ($container->getAliases() as $id => $alias) {
            if (strpos($id, 'SimPod\KafkaBundle\Kafka') === false) {
                continue;
            }
            $alias->setPublic(true);
        }
    }
}
