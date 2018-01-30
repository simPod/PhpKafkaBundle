<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicConf;

class Configuration
{
    /** @var Conf */
    private $config;

    public function __construct(string $groupId)
    {
        $config = new Conf();
        $config->setDefaultTopicConf(new TopicConf());
        $this->config = $config;
        $this->set(ConfigConstants::GROUP_ID, $groupId);
    }

    public function set(string $name, string $value) : void
    {
        $this->config->set($name, $value);
    }

    public function commitIfAutoCommitDisabled(Message $message, KafkaConsumer $kafkaConsumer) : void
    {
        if ($this->isAutoCommitEnabled()) {
            return;
        }
        $kafkaConsumer->commit($message);
    }

    public function isAutoCommitEnabled() : bool
    {
        return $this->config->dump()[ConfigConstants::ENABLE_AUTO_COMMIT] === 'true';
    }

    public function get() : Conf
    {
        return $this->config;
    }
}
