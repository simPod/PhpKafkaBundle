<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests\FullRun;

use RdKafka\Message;
use RdKafka\TopicConf;
use SimPod\KafkaBundle\Kafka\Consumer\Configuration;
use SimPod\KafkaBundle\Kafka\Consumer\Consumer;

final class TestConsumer extends Consumer
{
    /** @var callable */
    private $onConsume;

    public function __construct(callable $onConsume)
    {
        $this->onConsume = $onConsume;
    }

    public function getConfiguration() : Configuration
    {
        $configuration = new Configuration($this->getGroupId(), 1);

        $topicConf = new TopicConf();
        $topicConf->set('auto.offset.reset', 'earliest');
        $configuration->setDefaultTopicConf($topicConf);

        return $configuration;
    }

    public function getGroupId() : string
    {
        return 'test';
    }

    public function consume(Message $kafkaMessage) : void
    {
        ($this->onConsume)($kafkaMessage);
    }

    /**
     * @return string[]
     */
    public function getTopics() : array
    {
        return [FullRunTest::TEST_TOPIC];
    }
}
