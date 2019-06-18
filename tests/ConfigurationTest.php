<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests;

use SimPod\KafkaBundle\Kafka\Brokers;

final class ConfigurationTest extends KafkaTestCase
{
    public function testConnectionConfiguration() : void
    {
        $container = $this->createYamlBundleTestContainer();

        $brokers = $container->get(Brokers::class);
        self::assertSame('127.0.0.1:9092', $brokers->getBootstrapServers());
    }
}
