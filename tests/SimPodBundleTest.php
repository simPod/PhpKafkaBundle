<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests;

use PHPUnit\Framework\TestCase;
use SimPod\KafkaBundle\DependencyInjection\ConsumerCompilerPass;
use SimPod\KafkaBundle\SimPodKafkaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SimPodBundleTest extends TestCase
{
    /** @var SimPodKafkaBundle */
    private $bundle;

    public function testBuild() : void
    {
        $containerBuilder = new ContainerBuilder();
        $this->bundle->build($containerBuilder);
        $passConfig                   = $containerBuilder->getCompiler()->getPassConfig();
        $beforeOptimizationPasses     = $passConfig->getBeforeOptimizationPasses();
        $containsConsumerCompilerPass = false;
        foreach ($beforeOptimizationPasses as $compilerPass) {
            if (! $compilerPass instanceof ConsumerCompilerPass) {
                continue;
            }

            $containsConsumerCompilerPass = true;
        }
        self::assertTrue(
            $containsConsumerCompilerPass,
            'RabbitMQ bundle does not have registered consumer compiler pass.'
        );
    }

    protected function setUp() : void
    {
        $this->bundle = new SimPodKafkaBundle();
    }
}
