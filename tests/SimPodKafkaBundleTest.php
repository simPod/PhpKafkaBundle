<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests;

use PHPUnit\Framework\TestCase;
use SimPod\KafkaBundle\DependencyInjection\ConsumerCompilerPass;
use SimPod\KafkaBundle\SimPodKafkaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SimPodKafkaBundleTest extends TestCase
{
    public function testBuild() : void
    {
        $containerBuilder = new ContainerBuilder();
        (new SimPodKafkaBundle())->build($containerBuilder);

        $passConfig                       = $containerBuilder->getCompiler()->getPassConfig();
        $beforeOptimizationPasses         = $passConfig->getBeforeOptimizationPasses();
        $consumerCompilerPassIsRegistered = false;
        foreach ($beforeOptimizationPasses as $pass) {
            if ($pass instanceof ConsumerCompilerPass) {
                $consumerCompilerPassIsRegistered = true;
                continue;
            }
        }

        $this->assertTrue($consumerCompilerPassIsRegistered);
    }
}
