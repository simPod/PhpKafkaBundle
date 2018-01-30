<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle;

use SimPod\Bundle\KafkaBundle\Consumer\CompilerPass;
use SimPod\Bundle\KafkaBundle\Consumer\Consumer;
use SimPod\Bundle\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class KafkaBundle extends Bundle
{
    public function build(ContainerBuilder $container) : void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(Consumer::class)
            ->addTag(CompilerPass::TAG_NAME_CONSUMER);
        $container->addCompilerPass(new CompilerPass());
    }

    public function getContainerExtension() : KafkaExtension
    {
        if ($this->extension === null) {
            $this->extension = new KafkaExtension();
        }

        return $this->extension;
    }
}
