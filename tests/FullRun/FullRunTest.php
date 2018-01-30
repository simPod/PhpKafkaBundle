<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests\FullRun;

use RdKafka\KafkaConsumer;
use RdKafka\Message;
use SimPod\KafkaBundle\Kafka\Consumer\ConsumerRunner;
use SimPod\KafkaBundle\Kafka\Producer;
use SimPod\KafkaBundle\Tests\KafkaTestCase;

final class FullRunTest extends KafkaTestCase
{
    private const PAYLOAD    = 'Tasty, chilled pudding is best flavored with juicy lime.';
    public const  TEST_TOPIC = 'test-topic';

    public function testRun() : void
    {
        $container = $this->createYamlBundleTestContainer();

        $testProducer = new TestProducer($container->get(Producer::class));
        $testProducer->produce(self::PAYLOAD, self::TEST_TOPIC);

        /** @var ConsumerRunner $consumerRunner */
        $consumerRunner = $container->get(ConsumerRunner::class);

        $consumer = new TestConsumer(
            static function (Message $message) : void {
                self::assertSame(self::PAYLOAD, $message->payload);
            }
        );
        $consumerRunner->run($consumer);
    }

    public function consume(Message $kafkaMessage, KafkaConsumer $kafkaConsumer) : void
    {
        self::assertSame(self::PAYLOAD, $kafkaMessage->payload);
    }
}
