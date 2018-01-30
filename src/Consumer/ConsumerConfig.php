<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicConf;

class ConsumerConfig
{
    /** @var Conf */
    private $config;

    public function __construct(string $groupId)
    {
        $config = new Conf();
        $config->set(ConfigConstants::GROUP_ID, $groupId);
        $config->setDefaultTopicConf(new TopicConf());
        $this->config = $config;
    }

    public function disableAutoCommit() : void
    {
        $this->config->set(ConfigConstants::ENABLE_AUTO_COMMIT, 'false');
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
