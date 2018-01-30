<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Tests;

use PHPUnit\Framework\TestCase;
use SimPod\Bundle\KafkaBundle\Consumer\CompilerPass;
use SimPod\Bundle\KafkaBundle\KafkaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class KafkaBundleTest extends TestCase
{
    public function testBuild() : void
    {
        $containerBuilder = new ContainerBuilder();
        (new KafkaBundle())->build($containerBuilder);

        $passConfig                       = $containerBuilder->getCompiler()->getPassConfig();
        $beforeOptimizationPasses         = $passConfig->getBeforeOptimizationPasses();
        $consumerCompilerPassIsRegistered = false;
        foreach ($beforeOptimizationPasses as $pass) {
            if ($pass instanceof CompilerPass) {
                $consumerCompilerPassIsRegistered = true;
                continue;
            }
        }

        $this->assertTrue($consumerCompilerPassIsRegistered);
    }
}
