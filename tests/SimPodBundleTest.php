<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SimPod\KafkaBundle\DependencyInjection\ConsumerCompilerPass;
use SimPod\KafkaBundle\SimPodKafkaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;

#[CoversClass(SimPodKafkaBundle::class)]
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
            'Kafka bundle does not have registered consumer compiler pass.'
        );

        $autoconfiguredInstanceof = $containerBuilder->getAutoconfiguredInstanceof();
        self::assertArrayHasKey(
            NamedConsumer::class,
            $autoconfiguredInstanceof,
            'Kafka bundle does not have registered autoconfigured instance of.'
        );

        self::assertArrayHasKey(
            ConsumerCompilerPass::TAG_NAME_CONSUMER,$autoconfiguredInstanceof[NamedConsumer::class]->getTags());
    }

    protected function setUp() : void
    {
        $this->bundle = new SimPodKafkaBundle();
    }
}
