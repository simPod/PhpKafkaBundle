<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests\Kafka;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use SimPod\KafkaBundle\Kafka\Configuration;
use SimPod\KafkaBundle\Tests\KafkaTestCase;

/** @covers \SimPod\KafkaBundle\Kafka\Configuration */
final class ConfigurationTest extends KafkaTestCase
{
    #[DataProvider('providerConfiguration')]
    public function testConfiguration(
        string $config,
        ?string $authentication,
        string $bootstrapServers,
        ?string $clientId
    ) : void {
        $container = $this->createYamlBundleTestContainer($config);

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);
        self::assertSame($authentication, $configuration->getAuthentication());
        self::assertSame($bootstrapServers, $configuration->getBootstrapServers());
        self::assertSame($clientId, $configuration->getClientId());
    }

    /** @return Generator<array{string, string|null, string, string|null}> */
    public static function providerConfiguration() : Generator
    {
        yield ['test-no-auth-no-client', null, '127.0.0.1:9092', null];
        yield ['test-sasl-auth-some-id', 'sasl-plain://user:password', '127.0.0.1:9092,127.0.1.1:9092', 'kafka-bundle'];
    }
}
