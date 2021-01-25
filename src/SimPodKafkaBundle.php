<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle;

use SimPod\KafkaBundle\DependencyInjection\ConsumerCompilerPass;
use SimPod\KafkaBundle\DependencyInjection\KafkaExtension;
use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use function assert;

final class SimPodKafkaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(NamedConsumer::class)
            ->addTag(ConsumerCompilerPass::TAG_NAME_CONSUMER);
        $container->addCompilerPass(new ConsumerCompilerPass());
    }

    public function getContainerExtension(): KafkaExtension
    {
        if ($this->extension === null) {
            $this->extension = new KafkaExtension();
        }

        assert($this->extension instanceof KafkaExtension);

        return $this->extension;
    }
}
